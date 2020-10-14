<?php

namespace App\Http\Controllers;

use App\Memo;
use Cache;
use Illuminate\Http\Request;

class Top extends Controller
{
    public function __construct()
    {
        $this->middleware('login');
    }

    public function index(Request $request)
    {
        $folderId = $request->get('folder_id', '00000');

        return view('index', [
            'folderId' => $folderId,
            'rows' => Memo::getList($folderId),
            'folders' => Memo::folders($folderId),
        ]);
    }

    public function indexPost(Request $request)
    {
        $postFolderId = $request->post('folder_id', '00000');
        $parentFolderId = $request->get('folder_id', '00000');
        if ($postFolderId) {
            Memo::updateFolder($postFolderId, $request->post('folder_name'));
        } else {
            Memo::addFolder($request->post('folder_name'), $parentFolderId);
        }

        return redirect('/?folder_id=' . $parentFolderId);
    }

    public function deleteFolder(Request $request)
    {
        Memo::deleteFolder($request->post('folder_id'));
        return redirect('/');
    }

    public function memo(Request $request)
    {
        $folderId = $request->get('folder_id', 0);
        $memoId = $request->get('id', 0);

        $memo = Memo::getEntry($memoId);

        return view('memo', [
            'folderId' => $folderId,
            'memoId' => $memoId,
            'title' => $memo['title'],
            'updated_at' => date('Y-m-d H:i', strtotime($memo['updated_at'])),
            'body' => Memo::markDown($memo['body']),
            'folders' => Memo::folders($folderId),
            'tree' => Memo::folderTree(),
        ]);
    }

    public function delete(Request $request)
    {
        Memo::delete($request->post('memo_id'));
        return redirect('/?folder_id=' . $request->post('folder_id'));
    }

    public function moveMemo(Request $request)
    {
        Memo::moveMemo(
            $request->post('memo_id'),
            $request->post('folder_id')
        );
        return redirect('/?folder_id=' . $request->post('folder_id'));
    }

    public function form(Request $request)
    {
        $folderId = $request->get('folder_id', '00000');
        $memoId = $request->get('id', '00000');
        $folders = Memo::folders($folderId);

        if ((int)$memoId) {
            $memo = Memo::getEntry($memoId);
            $params = [
                'folderId' => $folderId,
                'memoId' => $memoId,
                'title' => $memo['title'],
                'updated_at' => $memo['updated_at'],
                'body' => $memo['body'],
                'folders' => $folders,
            ];
        } else {
            $params = [
                'folderId' => $folderId,
                'memoId' => 0,
                'title' => null,
                'updated_at' => null,
                'body' => null,
                'folders' => $folders,
            ];
        }

        return view('form', $params);
    }

    public function formPost(Request $request)
    {
        $folderId = $request->post('folder_id', '00000');
        $memoId = $request->post('id', '00000');

        if ($memoId) {
            Memo::update([
                'title' => $request->post('title'),
                'body' => $request->post('body'),
                'folder_id' => $folderId,
                'memo_id' => $memoId,
            ]);
        } else {
            $memoId = Memo::insert([
                'title' => $request->post('title'),
                'body' => $request->post('body'),
                'folder_id' => $folderId,
            ]);
        }

        return redirect(sprintf('/memo?id=%s&folder_id=%s', $memoId, $folderId));
    }

    public function image(Request $request, $key)
    {
        $image = Cache::get($key);
        return response($image['data'])->withHeaders([
            'Content-Type' => $image['type'],
            'Content-Length' => $image['size'],
        ]);
    }
}
