<?php
function smarty_function_readloginhead($paramer,$template){
	global $config,$seo;
	if($_COOKIE['uid']!=""&&$_COOKIE['username']!=""){
        if($_COOKIE['remind_num']>0){
            $html.='<div class="header_Remind header_Remind_hover"> <em class="header_Remind_em "><i class="header_Remind_msg"></i></em><div class="header_Remind_list" style="display:none;">';
            if($_COOKIE['usertype']==1){
                $html.='<div class="header_Remind_list_a"><a href="'.$config['sy_weburl'].'/member/index.php?c=msg">��������</a><span class="header_Remind_list_r fr">('.$_COOKIE['userid_msg'].')</span></div><div class="header_Remind_list_a"><a href="'.$config['sy_weburl'].'/friend/index.php?c=applyfriend">�������</a><span class="header_Remind_list_r fr">('.$_COOKIE['friend1'].')</span></div><div class="header_Remind_list_a"><a href="'.$config['sy_weburl'].'/member/index.php?c=xin">վ����</a><span class="header_Remind_list_r fr">('.$_COOKIE['friend_message1'].')</span></div><div class="header_Remind_list_a"><a href="'.$config['sy_weburl'].'/member/index.php?c=sysnews">ϵͳ��Ϣ</a><span class="header_Remind_list_r fr">('.$_COOKIE['sysmsg1'].')</span></div><div class="header_Remind_list_a"><a href="'.$config['sy_weburl'].'/member/index.php?c=commsg">��ҵ�ظ���ѯ</a><span class="header_Remind_list_r fr">('.$_COOKIE['usermsg'].')</span></div>';
            }elseif($_COOKIE['usertype']==2){
                $html.='<div class="header_Remind_list_a"><a href="'.$config['sy_weburl'].'/member/index.php?c=hr"class="fl">����ְλ</a><span class="header_Remind_list_r fr">('.$_COOKIE['userid_job'].')</span></div><div class="header_Remind_list_a"><a href="'.$config['sy_weburl'].'/friend/index.php?c=applyfriend"class="fl">�������</a><span class="header_Remind_list_r fr">('.$_COOKIE['friend2'].')</span></div><div class="header_Remind_list_a"><a href="'.$config['sy_weburl'].'/member/index.php?c=xin"class="fl">վ����</a><span class="header_Remind_list_r fr">('.$_COOKIE['friend_message2'].')</span></div><div class="header_Remind_list_a"><a href="'.$config['sy_weburl'].'/member/index.php?c=sysnews" class="fl"> ϵͳ��Ϣ</a><span class="header_Remind_list_r fr">('.$_COOKIE['sysmsg2'].')</span></div><div class="header_Remind_list_a"><a href="'.$config['sy_weburl'].'/member/index.php?c=msg"class="fl">��ְ��ѯ</a><span class="header_Remind_list_r fr">('.$_COOKIE['commsg'].')</span></div>';
            }elseif($_COOKIE['usertype']==3){
                $html.='<div class="header_Remind_list_a"><a href="'.$config['sy_weburl'].'/member/index.php?c=yp_resume"class="fl">ӦƸ����</a><span class="header_Remind_list_r fr">('.$_COOKIE['userid_job3'].')</span></div><div class="header_Remind_list_a"><a href="'.$config['sy_weburl'].'/member/index.php?c=entrust_resume" class="fl">ί�м���</a><span class="header_Remind_list_r fr">('.$_COOKIE['entrust'].')</span></div><div class="header_Remind_list_a"><a href="'.$config['sy_weburl'].'/friend/index.php?c=applyfriend"class="fl">�������</a><span class="header_Remind_list_r fr">('.$_COOKIE['friend3'].')</span></div><div class="header_Remind_list_a"><a href="'.$config['sy_weburl'].'/member/index.php?c=xin"class="fl"> վ����<span class="header_Remind_list_r fr">('.$_COOKIE['friend_message3'].'��</span></a></div><div class="header_Remind_list_a"><a href="'.$config['sy_weburl'].'/member/index.php?c=sysnews"class="fl"> ϵͳ��Ϣ</a><span class="header_Remind_list_r fr">('.$_COOKIE['sysmsg3'].')</span></div><div class="header_Remind_list_a"><a href="'.$config['sy_weburl'].'/member/index.php?c=zixun"class="fl">��ְ��ѯ</a><span class="header_Remind_list_r fr">('.$_COOKIE['commsg3'].')</span></div>';
            }elseif($_COOKIE['usertype']==4){
                $html.='<div class="header_Remind_list_a"><a href="'.$config['sy_weburl'].'/member/index.php?c=sign_up"class="fl">�γ�ԤԼ</a><span class="header_Remind_list_r fr">('.$_COOKIE['sign_up'].')</span></div><div class="header_Remind_list_a"><a href="'.$config['sy_weburl'].'/friend/index.php?c=applyfriend"class="fl">�������</a><span class="header_Remind_list_r fr">('.$_COOKIE['friend4'].')</span></div><div class="header_Remind_list_a"><a href="'.$config['sy_weburl'].'/member/index.php?c=xin"class="fl"> վ����</a><span class="header_Remind_list_r fr">('.$_COOKIE['friend_message4'].')</span></div><div class="header_Remind_list_a"><a href="'.$config['sy_weburl'].'/member/index.php?c=sysnews"class="fl"> ϵͳ��Ϣ</a><span class="header_Remind_list_r fr">('.$_COOKIE['sysmsg4'].')</span></div><div class="header_Remind_list_a"><a href="'.$config['sy_weburl'].'/member/index.php?c=message"class="fl">��ѯ����</a><span class="header_Remind_list_r fr">('.$_COOKIE['message'].')</span></div>';
            }
            $html.='</div> </div>';
        }
        $html2= "���ã�<a href=\"".$config['sy_weburl']."/member\" ><font color=\"red\">".$_COOKIE['username']."</font></a>��<a href=\"javascript:void(0)\" onclick=\"logout(\'".$config['sy_weburl']."/index.php?c=logout\');\">[��ȫ�˳�]</a>";

        $html.='<div class=" fr">'.$html2.'</div>';
    }else{
        $login_url = Url("index","login",array(),"1");
        $login_lt_url = Lurl(array("url"=>"c:login"));
        $login_train_url = turl(array("url"=>"c:login"));
        $reg_url = Url("index","register",array("usertype"=>"1"),"1");
        $reg_com_url = Url("index","register",array("usertype"=>"2"),"1");
        $reg_lt_url = Lurl(array("url"=>"c:register"));			
        $reg_train_url = turl(array("url"=>"c:register"));			
        $style = $config['sy_weburl']."/app/template/".$config['style'];

        $login='<li><a href="'.$login_url.'">��Ա��¼</a></li>';		
        $lt_login='<li><a href="'.$login_lt_url.'">��ͷ��¼</a></li>';			
        $train_login='<li><a href="'.$login_train_url.'">��ѵ��¼</a></li>';			
        $user_reg='<li><a href="'.$reg_url.'">����ע��</a></li>';
        $com_reg='<li><a href="'.$reg_com_url.'">��ҵע��</a></li>';
        $lt_reg='<li><a href="'.$reg_lt_url.'">��ͷע��</a></li>';
        $train_reg='<li><a href="'.$reg_train_url.'">��ѵע��</a></li>'; 
        if($_GET['f']=='l'){
            $html='<div class=" fr"><div class="yun_topLogin_cont"><div class="yun_topLogin"><a href="'.$login_lt_url.'">��ͷ��¼</a></div><div class="yun_topLogin"><a href="'.$reg_lt_url.'">��ͷע��</a></div></div></div>';
        }else{
            $html='<div class=" fr"><div class="yun_topLogin_cont"><div class="yun_topLogin"><a class="yun_More" href="javascript:void(0)">�û���¼</a><ul class="yun_Moredown" style="display:none">'.$login.$lt_login.$train_login.'</ul></div><div class="yun_topLogin"> <a class="yun_More" href="javascript:void(0)">�û�ע��</a><ul class="yun_Moredown fn-hide" style="display:none">'.$user_reg.$com_reg.$lt_reg.$train_reg.'</ul></div></div></div>';
            if($config['sy_qqlogin']=='1'||$config['sy_sinalogin']=='1'||$config['sy_wxlogin']=='1'){
                $flogin='<div class="fastlogin fr">';
                if($config['sy_qqlogin']=='1'){
                    $flogin.='<span style="width:70px;"><img src="'.$config['sy_weburl'].'/app/template/'.$config['style'].'/images/yun_qq.png" class="png" > <a href="'.Url("index","qqconnect",array("c"=>"qqlogin"),'1').'">QQ��¼</a></span>';
                }
                if($config['sy_sinalogin']=='1'){
                    $flogin.='<span><img src="'.$config['sy_weburl'].'/app/template/'.$config['style'].'/images/yun_sina.png" class="png"> <a href="'.Url("index","sinaconnect",array(),"1").'">����</a></span>';
                } 
                if($config['sy_wxlogin']=='1'){
                    $flogin.='<span><img src="'.$config['sy_weburl'].'/app/template/'.$config['style'].'/images/yun_wx.png" class="png"> <a href="'.Url("index","wxconnect",array(),"1").'">΢��</a></span>';
                }  
                $flogin.='</div>';
                $html.=$flogin;
            }
        }
    }
	return $html;
}
?>