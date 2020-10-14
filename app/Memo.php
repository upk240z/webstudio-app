<?php


namespace App;


use DateTime;
use Google\Cloud\Core\Timestamp;
use Google\Cloud\Firestore\FirestoreClient;

class Memo
{
    /**
     * @var FirestoreClient null
     */
    private static $firestore = null;

    private static function connect()
    {
        if (self::$firestore == null) {
            self::$firestore = new FirestoreClient();
        }
    }

    private static function id($id)
    {
        return sprintf('%05d', $id);
    }

    public static function getList($folderId = 0)
    {
        self::connect();

        $docs = self::$firestore->collection('folder')
            ->where('parent_id', '=', self::id($folderId))
            ->orderBy('folder_name')
            ->documents();

        $rows = [];

        foreach ($docs as $row) {
            $rows[] = [
                'is_folder' => true,
                'folder_id' => $row['id'],
                'folder_name' =>  $row['folder_name']
            ];
        }

        $docs = self::$firestore->collection('memo')
            ->where('folder_id', '=', self::id($folderId))
            ->orderBy('title')
            ->documents();


        foreach ($docs as $row) {
            $rows[] = [
                'is_folder' => false,
                'memo_id' => $row['id'],
                'title' => $row['title'],
                'body' => $row['body'],
                'created_at' => date('Y-m-d H:i', strtotime($row['created_at'])),
                'updated_at' => date('Y-m-d H:i', strtotime($row['updated_at'])),
            ];
        }

        return $rows;
    }

    public static function getFolder($folderId)
    {
        self::connect();
        $doc = self::$firestore->collection('folder')
            ->document(self::id($folderId))
            ->snapshot();
        return isset($doc['id']) ? $doc : null;
    }

    public static function getEntry($memo_id)
    {
        self::connect();
        $doc = self::$firestore->collection('memo')
            ->document(self::id($memo_id))
            ->snapshot();
        return isset($doc['id']) ? $doc : null;
    }

    private static function convertBody($body)
    {
        return str_replace(
            "\t",
            "    ",
            preg_replace("/(\r\n)/", "\n", $body)
        );
    }

    private static function createMemoId()
    {
        self::connect();

        $id = 1;
        $docs = self::$firestore->collection('memo')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->documents();
        if (!$docs->isEmpty()) {
            foreach ($docs as $doc) {
                $id = (int)$doc['id'] + 1;
                break;
            }
        }

        return self::id($id);
    }

    public static function insert($columns)
    {
        self::connect();

        $id = self::createMemoId();
        $folderId = self::id($columns['folder_id']);
        self::$firestore->collection('memo')->document($id)
            ->set([
                'id' => $id,
                'title' => $columns['title'],
                'body' => self::convertBody($columns['body']),
                'folder_id' => $folderId,
                'created_at' => new Timestamp(new DateTime()),
                'updated_at' => new Timestamp(new DateTime()),
            ]);

        if (!$id) {
            // 登録できなかった場合
            throw new Exception("登録に失敗");
        }

        return $id;
    }

    public static function update($columns)
    {
        self::connect();

        $id = self::id($columns['memo_id']);
        self::$firestore->collection('memo')->document($id)
            ->set([
                'title' => $columns['title'],
                'body' => self::convertBody($columns['body']),
                'updated_at' => new Timestamp(new DateTime()),
            ], ['merge' => true]);
    }

    public static function moveMemo($memoId, $folderId)
    {
        self::connect();

        $id = self::id($memoId);
        $folderId = self::id($folderId);

        self::$firestore->collection('memo')->document($id)
            ->set([
                'folder_id' => $folderId,
                'updated_at' => new Timestamp(new DateTime()),
            ], ['merge' => true]);
    }

    public static function delete($memoId)
    {
        self::connect();

        $id = self::id($memoId);
        self::$firestore->collection('memo')->document($id)->delete();
    }

    public static function markDown($text)
    {
        $md = new \Parsedown();
        $mdText = $md->text($text);

        $mdText = str_replace(
            '<table>',
            '<table class="table table-striped table-bordered">',
            $mdText
        );

        $mdText = str_replace(
            '<pre>',
            '<pre class="border border-dark rounded bg-light p-2">',
            $mdText
        );

        return $mdText;
    }

    private static function createFolderId()
    {
        self::connect();

        $id = 1;
        $docs = self::$firestore->collection('folder')
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->documents();
        if (!$docs->isEmpty()) {
            foreach ($docs as $doc) {
                $id = (int)$doc['id'] + 1;
                break;
            }
        }

        return self::id($id);
    }

    public static function addFolder($name, $parentId)
    {
        self::connect();

        $id = self::createFolderId();
        $parentId = self::id($parentId);

        self::$firestore->collection('folder')->document($id)
            ->set([
                'id' => $id,
                'folder_name' => $name,
                'parent_id' => $parentId
            ]);
    }

    public static function updateFolder($folderId, $name)
    {
        self::connect();

        $id = self::id($folderId);
        self::$firestore->collection('folder')->document($id)
            ->set([
                'folder_name' => $name,
            ], ['merge' => true]);
    }

    public static function deleteFolder($folderId)
    {
        self::connect();
        $id = self::id($folderId);
        self::$firestore->collection('folder')->document($id)->delete();
    }

    public static function folders($folderId)
    {
        self::connect();

        $folders = [];
        $cursol = $folderId;
        while (true) {
            if ($cursol == 0) {
                $folders[] = [
                    'id' => $cursol,
                    'name' => 'TOP'
                ];
                break;
            } else {
                $id = self::id($cursol);
                $folder = self::$firestore->collection('folder')->document($id)
                    ->snapshot();
                $folders[] = [
                    'id' => $cursol,
                    'name' => $folder['folder_name']
                ];
                $cursol = $folder['parent_id'];
            }
        }

        return array_reverse($folders);
    }

    private static function findChildrenFolder($folders, $parentId)
    {
        $children = [];
        foreach ($folders[$parentId] as $folder) {
            $_children = [];
            if (isset($folders[$folder['folder_id']])) {
                $_children = self::findChildrenFolder($folders, $folder['folder_id']);
            }
            $children[] = [
                'id' => $folder['folder_id'],
                'name' => $folder['folder_name'],
                'children' => $_children
            ];
        }

        return $children;
    }

    public static function folderTree()
    {
        self::connect();

        $docs = self::$firestore->collection('folder')
            ->orderBy('parent_id')
            ->orderBy('folder_name')
            ->documents();
        $folders = [];
        foreach ($docs as $doc) {
            $parentId = $doc['parent_id'];
            if (!isset($folders[$parentId])) {
                $folders[$parentId] = [];
            }

            $parents = self::$firestore->collection('folder')
                ->where('id', '=', $parentId)
                ->orderBy('folder_name')
                ->documents();

            if ($parentId == '00000') {
                $folders[$parentId][] = [
                    'folder_id' => $doc['id'],
                    'folder_name' => $doc['folder_name'],
                    'parent_name' => null,
                ];
            }

            foreach ($parents as $parent) {
                $folders[$parentId][] = [
                    'folder_id' => $doc['id'],
                    'folder_name' => $doc['folder_name'],
                    'parent_name' => $parent['folder_name'],
                ];
            }

        }

        $tree = [];
        foreach ($folders['00000'] as $folder) {
            $parents = [];
            if (isset($folders[$folder['folder_id']])) {
                $parents = self::findChildrenFolder($folders, $folder['folder_id']);
            }
            $tree[] = [
                'id' => $folder['folder_id'],
                'name' => $folder['folder_name'],
                'children' => $parents
            ];
        }

        return $tree;
    }

}
