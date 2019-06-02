<?php


namespace App;


class Memo
{
    public static function getList($folderId = 0)
    {
        $rows = [];

        $sql = '
            SELECT * FROM folder WHERE parent_id = :parent_id
            ORDER BY folder_name
        ';

        foreach (Util::object2array(\DB::select($sql, ['parent_id' => $folderId])) as $row) {
            $rows[] = [
                'is_folder' => true,
                'folder_id' => $row['folder_id'],
                'folder_name' =>  $row['folder_name']
            ];
        }

        $sql = '
            SELECT * FROM memo WHERE folder_id = :folder_id
            ORDER BY title
        ';
        foreach (Util::object2array(\DB::select($sql, ['folder_id' => $folderId])) as $row) {
            $row['is_folder'] = false;
            if (!$row['updated_at']) $row['updated_at'] = $row['created_at'];
            $rows[] = $row;
        }

        return $rows;
    }

    public static function getFolder($folderId)
    {
        $rows = Util::object2array(
          \DB::select('SELECT * FROM folder WHERE folder_id = ?', [$folderId])
        );
        return count($rows) > 0 ? $rows[0] : null;
    }

    public static function getEntry($memo_id)
    {
        $sql = "
            select * from memo where memo_id = ?
        ";
        $rows = Util::object2array(\DB::select($sql, [$memo_id]));
        if (count($rows) == 0) { return null; }

        $row = $rows[0];
        if (!$row["updated_at"]) {
            $row["updated_at"] = $row["created_at"];
        }

        return $row;
    }

    private static function convertBody($body)
    {
        return str_replace(
            "\t",
            "    ",
            preg_replace("/(\r\n)/", "\n", $body)
        );
    }

    public static function insert($columns)
    {
        $sql = "
            insert into memo (
                title, body, folder_id, created_at
            ) values (:title, :body, :folder_id, datetime('now', 'localtime'))
        ";

        \DB::insert($sql, array(
            'title' => $columns['title'],
            'body' => self::convertBody($columns['body']),
            'folder_id' => $columns['folder_id']
        ));

        $id = \DB::getPdo()->lastInsertId();

        if (!$id) {
            // 登録できなかった場合
            throw new Exception("登録に失敗");
        }

        return $id;
    }

    public static function update($columns)
    {
        $sql = "
            update memo set
                title = :title,
                body = :body,
                updated_at = datetime('now', 'localtime')
            where memo_id = :memo_id
        ";
        \DB::update($sql, array(
            'memo_id' => $columns['memo_id'],
            'title' => $columns['title'],
            'body' => self::convertBody($columns['body'])
        ));
    }

    public static function moveMemo($memoId, $folderId)
    {
        $sql = 'UPDATE memo set folder_id = :folder_id WHERE memo_id = :memo_id';
        \DB::update($sql, [
            'memo_id' => $memoId,
            'folder_id' => $folderId,
        ]);
    }

    public static function delete($no)
    {
        \DB::delete('delete from memo where memo_id = ?', [$no]);
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

    public static function addFolder($name, $parentId)
    {
        $sql = '
            INSERT INTO folder (folder_name, parent_id)
            VALUES (:folder_name, :parent_id)
        ';

        \DB::insert($sql, [
            'folder_name' => $name,
            'parent_id' => $parentId
        ]);
    }

    public static function updateFolder($folderId, $name)
    {
        $sql = '
            UPDATE folder set folder_name = :folder_name
            WHERE folder_id = :folder_id
        ';

        \DB::update($sql, [
            'folder_name' => $name,
            'folder_id' => $folderId
        ]);
    }

    public static function deleteFolder($folderId)
    {
        $sql = '
            DELETE FROM folder WHERE folder_id = :folder_id
        ';

        \DB::delete($sql, [
            'folder_id' => $folderId
        ]);
    }

    public static function folders($folderId)
    {
        $sql = 'SELECT parent_id, folder_name FROM folder WHERE folder_id = ?';

        $folders = [];
        $cursol = $folderId;
        while ($cursol != -1) {
            if ($cursol == 0) {
                $folders[] = [
                    'id' => $cursol,
                    'name' => 'TOP'
                ];
                $cursol = -1;
            } else {
                $rows = Util::object2array(\DB::select($sql, [$cursol]));
                $folder = $rows[0];
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
        $sql = '
            SELECT f.*, p.folder_name as parent_name
            FROM
                folder as f
                LEFT JOIN folder as p ON (f.parent_id = p.folder_id)
            ORDER BY f.parent_id, f.folder_name
        ';

        $folders = [];
        foreach (Util::object2array(\DB::select($sql)) as $row) {
            if (isset($folders[$row['parent_id']]) == false) {
                $folders[$row['parent_id']] = [];
            }
            $folders[$row['parent_id']][] = $row;
        }

        $tree = [];
        foreach ($folders[0] as $folder) {
            $children = [];
            if (isset($folders[$folder['folder_id']])) {
                $children = self::findChildrenFolder($folders, $folder['folder_id']);
            }
            $tree[] = [
                'id' => $folder['folder_id'],
                'name' => $folder['folder_name'],
                'children' => $children
            ];
        }

        return $tree;
    }

}
