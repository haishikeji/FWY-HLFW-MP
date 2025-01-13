<?php
/**
 * 首页
 * @author wesmiler
 */
namespace app\weixin\controller;
use app\weixin\model\Wechat;
use cmf\controller\HomeBaseController;

class TestController extends BaseController
{

    /**
     * 首页
     * @return mixed
     */
    public function index(){

        return $this->fetch(':index');
    }

    /**
     * 进入完善页面
     */
    public function entry(){
        $needRegProfile = session('needRegProfile');
        if($needRegProfile == false){
            Wechat::redirectUrl(url('/weixin/match/index','','',true));
        } 
        return $this->fetch('entry');
    }

    public function photo(){
        return $this->fetch('photo');
    }


}


?>