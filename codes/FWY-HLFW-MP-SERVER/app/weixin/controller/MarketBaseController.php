<?php
/**
 * 入口
 * @author wesmiler
 */
namespace app\weixin\controller;
use app\weixin\model\Fans;
use app\weixin\model\Member;
use app\weixin\model\Wechat;
use app\weixin\service\PRedis;
use cmf\controller\HomeBaseController;
use think\Request;

class MarketBaseController extends HomeBaseController
{
    public $userId = 0;
    public $userInfo = [];
    public $agentType = 2;
    public $agentStatus = 0;
    public function __construct(Request $request = null)
    {
        parent::__construct($request);

        // AJAX请求 不验证授权
        $field = 'id,openid,user_login,mobile,real_name,user_status,agent_type,agent_status,is_reg_profile,user_nickname,avatar,is_heart,is_follow,freezing_choose';
        if($this->request->isAjax()){
            $this->userInfo = session('agentInfo');
            $debug = config('weixin.debug');
            if(empty($this->userInfo) || $debug){
                // 调试模式
                $curOpenId = session('openid');
                $openid = $curOpenId? $curOpenId : config('weixin.openid');
                if($openid){
                    $this->userInfo = Member::getInfo(['openid'=> $openid, 'agent_type'=> 1],$field);
                }
                $wxInfo = Fans::getInfo(['openid' => $openid]);
                session('openid', $openid);
                session('wxInfo', $wxInfo);
                session('agentInfo', $this->userInfo);

            }
           
            $this->userId = isset($this->userInfo['id'])? intval($this->userInfo['id']) : 0;
            $this->userType = isset($this->userInfo['user_type'])? intval($this->userInfo['user_type']) : 2;
            $agentStatus = isset($this->userInfo['agent_status'])? intval($this->userInfo['agent_status']) : -1;
            if(empty($this->userId) || $agentStatus == 2){
                showJson(1005,2103,['url'=> Wechat::makeRedirectUrl(url('/weixin/market/entry','','',true))]);
            }
            return true;
        }


        // 调用微信授权校验
        $openid = '';
        $debug = config('weixin.debug');
        if(empty($debug)){
            $this->userInfo = session('agentInfo');
            $openid = session('openid');
            $cacheKey = "weixin:auth_agent:".$openid;
            if(empty($openid) || empty($this->userInfo) || !PRedis::get($cacheKey)){
                Wechat::auth();
                if($openid){
                    PRedis::set($cacheKey, session('agentInfo'), 7 * 24 * 3600);
                }
            }

        }else{
            $curOpenId = session('openid');
            $openid = $curOpenId? $curOpenId : config('weixin.openid');
            $this->userInfo = session('agentInfo');
            $cacheKey = "weixin:test:".$openid;
            if(empty($this->userInfo) || !PRedis::get($cacheKey)){
                if($openid){
                    $this->userInfo = Member::getInfo(['openid'=> $openid, 'user_type'=> 2],$field);
                }
                $wxInfo = Fans::getInfo(['openid' => $openid]);
                session('openid', $openid);
                session('wxInfo', $wxInfo);
                session('agentInfo', $this->userInfo);
                if($openid){
                    PRedis::set($cacheKey, session('agentInfo'), 7 * 24 * 3600);
                }
            }

            $this->userId = isset($this->userInfo['id'])? $this->userInfo['id'] : 0;
        }

        $this->agentType = isset($this->userInfo['agent_type'])? intval($this->userInfo['agent_type']) : 0;
        $this->agentStatus = isset($this->userInfo['agent_status'])? intval($this->userInfo['agent_status']) : 2;

    }

    /**
     * 验证用户状态
     */
    public function checkUserStatus(){
        // 验证用户是否冻结
        $agentStatus = Member::where(['id'=> $this->userId, 'agent_type'=> 1])->value('agent_status');
        if($agentStatus == 2) {
            Wechat::redirectUrl(url('/weixin/market/entry', '', '', true));
            exit;
        }else if($agentStatus != 1){
            Wechat::redirectUrl(url('/weixin/page/custom', '', '', true));
            exit;
        }
    }

}


?>