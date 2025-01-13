<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2018 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\weixin\validate;

use think\Validate;

class GoodValidate extends Validate
{
    protected $rule = [
        'goods_name' => 'require|chsDash|length:2,50',
        'sku' => 'require|chsDash|length:2,30',
        'score' => 'require|number|length:1,10',
        'stock' => 'require|number|length:1,10',
        'cate_id' => 'require|number|length:1,10',
        'albums' => 'require',
    ];
    protected $message = [
        'goods_name.require' => '商品名称不为空',
        'goods_name.*' => '商品名称格式不正确',
        'score.require' => '商品兑换所需积分不为空',
        'score.*' => '商品兑换所需积分格式错误',
        'sku.require' => '商品库存不为空',
        'sku.*' => '商品库存格式不正确',
        'cate_id.require' => '商品分类不为空',
        'cate_id.*' => '商品分类格式错误',
        'albums.require' => '商品相册不为空',
    ];

    protected $field = [
        'goods_name'=> '商品名称',
        'score'=> '兑换所需积分',
        'stock'=> '商品库存',
        'cate_id'=> '商品分类',
        'albums'=> '商品相册',
    ];

    protected $regex = [
        'mobile'=> '/^((13[0-9])|(14[5,7])|(15[0-9])|(16[6-8])|(17[0,1,3,5-8])|(18[0-9])|(19[8,9]))\\d{8}$/',
        'pwd'=> '/^[0-9a-zA-Z][0-9a-zA-Z\_\!\@\#\$\%\^\&\*\(\)]{2,19}/',
    ];

    protected $scene = [
        'info'  => ['goods_name','score','stock','cate_id','albums'],
    ];
}