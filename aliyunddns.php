<?php
/**
 * aliyunddns
 * 用的api版本为2015-01-09
 * Created by PhpStorm.
 * User: fthvgb1
 * Date: 2016/4/28
 * Time: 18:53
 */

/**
 * @return bool|string 公网ip
 */
function getip(){
    $curl=curl_init('http://ip.cip.cc');
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $res=curl_exec($curl);
    curl_close($curl);
    return trim($res);

}

$ip=getip();
date_default_timezone_set("UTC");//设置时区为utc
/**
 * @param $url string url地址
 * @return mixed  阿里云返回的结果
 */
function request_get($url){
    $curl=curl_init();//初始化curl
    curl_setopt($curl,CURLOPT_URL,$url);
    //referer头
    curl_setopt($curl,CURLOPT_AUTOREFERER,true);
    curl_setopt($curl,CURLOPT_HEADER,false);//不处理响应头
    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);//返回响应结果，不直接输出
    $response=curl_exec($curl);//发出请求
    return json_decode($response,true);
}

/**
 * 生成url
 * 这里只有公共的参数
 * @param array $param 需要去查看api文档里对应操作需要请求的参数
 * @return string   生成get请求对应白url
 */
function get_url($param=array()){
    $aliyun='alidns.aliyuncs.com?';
    $rand_num=rand(10000000,999999999);
    $time=time();
    $common=array(
        'Format'=>'JSON',
        'Version'=>'2015-01-09',
        'Timestamp'=>date('Y-m-d\TH:i:s\Z',$time),
        'SignatureMethod'=>'HMAC-SHA1',
        'AccessKeyId'=>'',//这里填你的AccessKeyid
        'SignatureVersion'=>'1.0',
        'SignatureNonce'=>$rand_num,
    );
    $all=array_merge($common,$param);
    ksort($all,SORT_NATURAL);
    $ur=http_build_query($all);
    $uri=rawurlencode($ur);
    $final_url='GET&'.rawurlencode('/').'&'.$uri;
    $sign=base64_encode(hash_hmac('sha1',$final_url,'填入Access Key Secret',true));//这里需要填secret
    $all['Signature']=$sign;
    $uurl=$aliyun.http_build_query($all);
    return $uurl;
};
$record_info_url=get_url(array('Action'=>'DescribeDomainRecords','DomainName'=>'域名'));//这里需要填域名
$record_info=request_get($record_info_url);//获取域名记录列表
//判断是否需要修改ip
//这里偷了个懒，本来应该遍历判断的，但默认的A WWW 就是 数组的第一元素，直接写0了，以后有问题再修改吧
if($ip!=$record_info['DomainRecords']['Record'][0]['Value']){
    //需要修改ip
    $update_param['Action']='UpdateDomainRecord';
    $update_param['RecordId']=$record_info['DomainRecords']['Record'][0]['RecordId'];
    $update_param['RR']='www';
    $update_param['Type']='A';
    $update_param['Value']=$ip;
    $update_url=get_url($update_param);
    $res=request_get($update_url);
}
exit;