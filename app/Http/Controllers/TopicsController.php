<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TopicRequest;
use App\Models\Category;
use Auth;
use App\Handlers\ImageUploadHandler;

class TopicsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

	public function index(Request $request, Topic $topic)
	{
		$topics = $topic->withOrder($request->order)->with('user','category')->paginate();
		
		return view('topics.index', compact('topics'));
	}

	//显示详细信息
    public function show(Topic $topic, Request $request)
    {
    	// URL 矫正
        if ( ! empty($topic->slug) && $topic->slug != $request->slug) {
            return redirect($topic->link(), 301);
        }

        return view('topics.show', compact('topic'));
    }

    //发帖
	public function create(Topic $topic)
	{
		$categories = Category::all();
        return view('topics.create_and_edit', compact('topic', 'categories'));
	}

	//处理发帖请求
	public function store(TopicRequest $request, Topic $topic)
	{
		//将数据写入到模型属性中
		$topic->fill($request->all());
		//写入当前用户id
		$topic->user_id = Auth::id();
		//写入数据库  save()方法执行会分发队列任务
		$topic->save();

		return redirect()->to($topic->link())->with('message', 'Created successfully.');
	}

	//编辑帖子页面
	public function edit(Topic $topic)
	{
       $this->authorize('update', $topic);
       $categories = Category::all();
       return view('topics.create_and_edit', compact('topic', 'categories'));
	}

	//处理编辑请求
	public function update(TopicRequest $request, Topic $topic)
	{
		$this->authorize('update', $topic);
		$topic->update($request->all());

		return redirect()->to($topic->link())->with('message', 'Updated successfully.');
	}

	//处理删除请求
	public function destroy(Topic $topic)
	{
		$this->authorize('destroy', $topic);
		$topic->delete();

		return redirect()->route('topics.index')->with('message', 'Deleted successfully.');
	}

	/**
	 * [uploadImage 上传图片]
	 * @param  Request            $request  [description]
	 * @param  ImageUploadHandler $uploader [App\Handlers\ImageUploadHandler.php]
	 * @return [type]                       [description]
	 */
	public function uploadImage(Request $request, ImageUploadHandler $uploader)
    {
        // 初始化返回数据，默认是失败的
        $data = [
            'success'   => false,
            'msg'       => '上传失败!',
            'file_path' => ''
        ];
        // 判断是否有上传文件，并赋值给 $file
        if ($file = $request->upload_file) {
            // 保存图片到本地
            $result = $uploader->save($file, 'topics', \Auth::id(), 1024);
            // 图片保存成功的话
            if ($result) {
                $data['file_path'] = $result['path'];
                $data['msg']       = "上传成功!";
                $data['success']   = true;
            }
        }
        return $data;
    }
}