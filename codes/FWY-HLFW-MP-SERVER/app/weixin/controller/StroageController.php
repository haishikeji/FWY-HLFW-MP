<?php
/**
 * 文件上传模块
 * @author wesmiler
 */
namespace app\weixin\controller;
use app\admin\model\CarModel;
use app\weixin\model\Commands;
use app\weixin\model\FileLogs;
use app\weixin\model\Member;
use app\weixin\model\Storage;
use app\weixin\model\Wechat;

class StroageController extends BaseController
{
    /**
     * 图片上传
     */
    public function image(){
        $params = input();
        $type = isset($params['type'])? $params['type'] : 1;
        $files = request()->file('image');
        $check = FileLogs::where(['user_id'=> $this->userId, 'type'=> $type,'status'=> 1])->count();
        if(empty($files) && empty($check)){
            showJson('error', 3000);
        }

        $imgList = [];
        $tempList = [];
        $nums = isset($params['nums'])? $params['nums'] : [];
        $colors = isset($params['colors'])? $params['colors'] : [];
        if($files){
            foreach ($files as $k => $file){
                if(empty($file)){
                    continue;
                }
                $fileData  = Storage::uploadImg($file,'print');
                $file = isset($fileData['file'])? $fileData['file'] : '';
                $num = isset($nums[$k])? $nums[$k] : 1;
                $md5Key = isset($fileData['md5_key'])? $fileData['md5_key'] : '';
                if($file){
//                if($file && $md5Key && !FileLogs::where(['md5_key'=> $md5Key])->value('id')){
                    $data = [
                        'url'=> $file,
                        'name'=> isset($fileData['name'])? $fileData['name'] : '',
                        'nums'=> $num,
                        'user_id'=> $this->userId,
                        'total_page'=> 1,
                        'color_type'=> isset($colors[$k])? intval($colors[$k]) : 1,
                        'md5_key'=> $md5Key,
                        'type'=> $type,
                        'file_size'=> isset($fileData['file_size'])? intval($fileData['file_size']) : 0,
                        'file_type'=> isset($fileData['file_type'])? $fileData['file_type'] : '',
                        'created_at'=> date('Y-m-d H:i:s')
                    ];
                    $imgList[] = $data;
                    $data['preview'] = cmf_get_image_preview_url($data['url']);
                    $tempList[] = $data;
                }
            }
        }

        //var_dump($imgList);exit;
        if($imgList){
            if($type == 3){
                FileLogs::where(['user_id'=> $this->userId, 'type'=> $type,'status'=> 1])->delete();
            }
            FileLogs::insertAll($imgList);
        }

        showJson('success', 3002, $tempList);
    }


    /**
     * 图片上传
     */
    public function thumb(){
        $file = request()->file('image');
        $scene = request()->get('scene','image');
        $fileData  = Storage::uploadImg($file, $scene);
        showJson('success', 3002, $fileData);
    }


    /**
     * 文件上传
     */
    public function file(){
        try {
            $file = request()->file('file');
            if(empty($file)){
                showJson('error', 3000);
            }

            $fileData  = Storage::uploadFile($file,'print');
            $file = isset($fileData['file'])? $fileData['file'] : '';
            if(empty($file)){
                showJson('error', 3001);
            }

            $totalPage = isset($fileData['total_page'])? $fileData['total_page'] : 0;
            if($totalPage <= 0){
                showJson('error', 3008);
            }
            $data = [
                'url'=> $file,
                'nums'=> 1,
                'user_id'=> $this->userId,
                'name'=> isset($fileData['name'])? $fileData['name'] : '',
                'md5_key'=> isset($fileData['md5_key'])? $fileData['md5_key'] : '',
                'total_page'=> $totalPage,
                'file_size'=> isset($fileData['file_size'])? intval($fileData['file_size']) : 0,
                'file_type'=> isset($fileData['file_type'])? $fileData['file_type'] : '',
                'type'=> 2,
                'created_at'=> date('Y-m-d H:i:s')
            ];
            $fileId = FileLogs::insertGetId($data);
            if($fileId){
                $fileData['id'] = $fileId;
                showJson('success', 3002, $fileData);
            }else{
                showJson('error', 3001);
            }
        } catch (\Exception $exception){
            showJson('error', $exception->getMessage());
        }
    }

    /**
     * 上传图片
     */
    public function baseImg(){
        $params = input();
        $imgList = isset($params['imgList'])? $params['imgList'] : [];
        if(empty($imgList)){
            showJson('error', 3005);
        }
        $result = Storage::uploadBaseImg($imgList);
        if($result){
            showJson('success', 3003);
        }else{
            showJson('error', 3003);
        }
    }

}


?>