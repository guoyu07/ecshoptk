<?php
class Smarty_Internal_Compile_Userlist extends Smarty_Internal_CompileBase{
    public $required_attributes = array('item');
    public $optional_attributes = array('name', 'key', 'post_len', 'limit', 'salary', 'idcard', 'edu', 'order', 'work', 'exp', 'sex', 'keyword', 'hy', 'provinceid', 'report', 'cityid', 'three_cityid', 'adtime', 'jobids', 'pic', 'typeids', 'type', 'job1_son', 'job_post', 'uptime', 'ispage', 'rec_resume','where_uid', 'height_status', 'rec', 't_len' ,'top');
    public $shorttag_order = array('from', 'item', 'key', 'name');
    public function compile($args, $compiler, $parameter){
        $_attr = $this->getAttributes($compiler, $args);

        $from = $_attr['from'];
        $item = $_attr['item'];
        $name = $_attr['name'];
        $name=str_replace('\'','',$name);
        $name=$name?$name:'list';$name='$'.$name;
        if (!strncmp("\$_smarty_tpl->tpl_vars[$item]", $from, strlen($item) + 24)) {
            $compiler->trigger_template_error("item variable {$item} may not be the same variable as at 'from'", $compiler->lex->taglineno);
        }

        //�Զ����ǩSTART
        $OutputStr=''.$name.'=array();global $db,$db_config,$config;eval(\'$paramer='.str_replace('\'','\\\'',ArrayToString($_attr,true)).';\');
		$ParamerArr = GetSmarty($paramer,$_GET);
		$paramer = $ParamerArr[arr];extract($paramer);
		$Purl =  $ParamerArr[purl];include PLUS_PATH."/job.cache.php";
		$where = "a.status<>\'2\' and a.`r_status`<>\'2\' and b.`job_classid`<>\'\' and b.`open`=\'1\' and a.`uid`=b.`uid`";
		//�Ƿ����ڷ�վ��
		if($config[\'sy_web_sit\']=="1"){
			if($_SESSION[\'cityid\']>0 && $_SESSION[\'cityid\']!=""){
				$paramer[\'cityid\']=$_SESSION[\'cityid\'];
			}
			if($_SESSION[\'hyclass\']>0 && $_SESSION[\'hyclass\']!=""){
				$paramer[\'hy\']=$_SESSION[\'hyclass\'];
			}
		}
		//��ע�ҹ�˾���˲�--����
		if($paramer[where_uid]){
			$where .=" AND a.`uid` in (".$paramer[\'where_uid\'].")";
		}
		//�����֤
		if($paramer[idcard]){
			$where .=" AND a.`idcard_status`=\'1\'";
		}
		//�߼��˲�
		if($paramer[height_status]){
			$where .=" AND b.height_status=.$paramer[height_status]";
		}else{
			$where .=" AND b.height_status<>\'2\' AND a.`def_job`=b.`id`";
		}
		//�߼��˲��Ƽ�
		if($paramer[rec]){
			$where .=" AND b.rec=1";
		}
		//��ͨ�Ƽ�
		if($paramer[rec_resume]){
			$where .=" AND b.`rec_resume`=1";
		}
		//��Ʒ
		if($paramer[work]){
			$show=$db->select_all("resume_show","1 group by eid","`eid`");
			if(is_array($show))
			{
				foreach($show as $v)
				{
					$eid[]=$v[\'eid\'];
				}
			}
			$where .=" AND b.id in (".@implode(",",$eid).")";
		}
		//����
		if($paramer[cid]){
			$where .= " AND (b.cityid=$paramer[cid] or b.three_cityid=$paramer[cid])";
		}
		//�ؼ���
		if($paramer[keyword]){
			$where1[]="b.`name` LIKE \'%$paramer[keyword]%\'";
			foreach($job_name as $k=>$v){
				if(strpos($v,$paramer[keyword])!==false){
					$jobid[]=$k;
				}
			}
			if(is_array($jobid))
			{
				foreach($jobid as $value)
				{
					$class[]="FIND_IN_SET(\'".$value."\',b.job_classid)";
				}
				$where1[]=@implode(" or ",$class);
			}
			include PLUS_PATH."/city.cache.php";
			foreach($city_name as $k=>$v)
			{
				if(strpos($v,$paramer[keyword])!==false)
				{
					$cityid[]=$k;
				}
			}
			if(is_array($cityid))
			{
				foreach($cityid as $value)
				{
					$class[]= "(b.provinceid = \'".$value."\' or b.cityid = \'".$value."\')";
				}
				$where1[]=@implode(" or ",$class);
			}
			$where.=" AND (".@implode(" or ",$where1).")";
		}
		//�Ƿ�����Ƭ
		if($paramer[pic]=="0"||$paramer[pic]){
			$where .=" AND a.photo<>\'\'";
		}
		//���Ʋ���Ϊ��
		if($paramer[name]=="0"){
			$where .=" AND a.name<>\'\'";
		}
		//��ְ��ҵ����Ϊ��
		if($paramer[hy]=="0"){
			$where .=" AND b.hy<>\'\'";
		}elseif($paramer[hy]!=""){
			$where .= " AND (b.`hy` IN (".$paramer[\'hy\']."))";
		}
		//ְλ���
		if($paramer[jobids]){
			$joball=explode(",",$paramer[jobids]);
			foreach(explode(",",$paramer[jobids]) as $v){
				if($job_type[$v]){
					$joball[]=@implode(",",$job_type[$v]);
				}
			}
			$job_classid=implode(",",$joball);
		}
		if($paramer[job1_son]){
			$joball=$job_type[$paramer[job1_son]];
			foreach($job_type[$paramer[job1_son]] as $v)
			{
				$joball[]=@implode(",",$job_type[$v]);
			}
			$job_classid=@implode(",",$joball);
		}
		if($job_classid){
			$classid=@explode(",",$job_classid);
			foreach($classid as $value){
				$class[]="FIND_IN_SET(\'".$value."\',b.job_classid)";
			}
			$classid=@implode(" or ",$class);
			$where .= " AND ($classid)";
		}
		if($paramer[job_post]){
			foreach($paramer[\'job_post\'] as $v){
				$jobwhere[]="FIND_IN_SET(\'".$v."\',b.job_classid)";
			}
			$jobwhere=implode(" or ",$jobwhere);
			$where .=" AND (".$jobwhere.")";
		}
		//���д���
		if($paramer[provinceid]){
			$where .= " AND b.provinceid = $paramer[provinceid]";
		}
		//��������
		if($paramer[cityid]){
			$where .= " AND (b.`cityid` IN ($paramer[cityid]))";
		}
		//������������
		if($paramer[three_cityid]){
			$where .= " AND (b.`three_cityid` IN ($paramer[three_cityid]))";
		}
		//��������,������ִ�иò�ѯ
		if($paramer[cityin]){
			$where .= " AND( AND b.provinceid IN ($paramer[cityin]) OR b.cityid IN ($paramer[cityin]) OR b.three_cityid IN ($paramer[cityin]))";
		}
		//��������
		if($paramer[exp]){
			$where .=" AND a.exp=$paramer[exp]";
		}else{
			$where .=" AND a.exp>0";
		}
		//ѧ��
		if($paramer[edu]){
			$where .=" AND a.edu=$paramer[edu]";
		}else{
			$where .=" AND a.edu>0";
		}
		//�Ա�
		if($paramer[sex]){
			$where .=" AND a.sex=$paramer[sex]";
		}
		//����ʱ��
		if($paramer[report]){
			$where .=" AND b.report=$paramer[report]";
		}
		//����ʱ��
		if($paramer[salary]){
			$where .=" AND b.salary=$paramer[salary]";
		}
		//��������
		if($paramer[type]){
			$where .= " AND b.type=$paramer[type]";
		}
		//����ʱ������
		if($paramer[uptime]){
			$time=time();
			$uptime = $time-($paramer[uptime]*86400);
			$where.=" AND b.lastupdate>$uptime";
		}
		//���ʱ�����䣬�����ʱ��
		if($paramer[adtime]){
			$time=time();
			$adtime = $time-($paramer[adtime]*86400);
			$where.=" AND b.status_time>$adtime";
		}
        //�����ֶ�Ĭ��Ϊ����ʱ�� edu
		if($paramer[order] && $paramer[order]!="lastdate"){
			if($paramer[order]==\'ant_num\'){
				$order = " ORDER BY a.`".str_replace("\'","",$paramer[order])."`";
			}elseif($paramer[order]==\'topdate\'){
				$nowtime=time();
				$order = " ORDER BY if(b.topdate>$nowtime,b.topdate,b.lastupdate)";
			}else{
				$order = " ORDER BY b.`".str_replace("\'","",$paramer[order])."`";
			}
		}else{
			$order = " ORDER BY b.lastupdate ";
		}
		//������� Ĭ��Ϊ����
		$sort = $paramer[sort]?$paramer[sort]:\'DESC\';
		//��ѯ����
		if($paramer[limit]){
			$limit=" LIMIT ".$paramer[limit];
		}
		$where.=$order.$sort;
		//�Զ����ѯ������Ĭ��ȡ�������κβ���ֱ��ʹ�ø����
		if($paramer[where]){
			$where = $paramer[where];
		}
		if($paramer[ispage]){
			if($paramer["height_status"]){
				$limit = PageNav($paramer,$_GET,"resume",$where,$Purl,"resume_expect","3",$_smarty_tpl);
			}else{
				$limit = PageNav($paramer,$_GET,"resume",$where,$Purl,"resume_expect",\'0\',$_smarty_tpl);
			}
		}
		'.$name.'=$db->select_alls("resume","resume_expect",$where.$limit,"b.*,a.*,a.name as username,b.provinceid as provinceid,b.cityid as cityid");
		if(is_array('.$name.')){
			//��������ֶ�
			$cache_array = $db->cacheget();
			$userclass_name = $cache_array["user_classname"];
			$city_name      = $cache_array["city_name"];
			$job_name		= $cache_array["job_name"];
			$industry_name	= $cache_array["industry_name"];
			$my_down=array();
			if($_COOKIE[\'usertype\']==\'2\')
			{
				$my_down=$db->select_all("down_resume","`comid`=\'".$_COOKIE[\'uid\']."\'","uid");
			}
			foreach('.$name.' as $k=>$v)
			{
				//����ʱ����ʾ��ʽ
				$time=time()-$v[\'lastupdate\'];
				if($time>86400 && $time<259300){
					'.$name.'[$k][\'time\'] = ceil($time/86400)."��ǰ";
				}elseif($time>3600 && $time<86400){
					'.$name.'[$k][\'time\'] = ceil($time/3600)."Сʱǰ";
				}elseif($time>60 && $time<3600){
					'.$name.'[$k][\'time\'] = ceil($time/60)."����ǰ";
				}elseif($time<60){
					'.$name.'[$k][\'time\'] = "�ո�";
				}else{
					'.$name.'[$k][\'time\'] = date("Y-m-d",$v[\'lastupdate\']);
				}
				//ͬʱ����������������Զ�ͷ����д���
				if($config[\'sy_usertype_1\']==\'1\'&&$v[\'photo\']){
					if(!empty($my_down)){
						foreach($my_down as $m_k=>$m_v){
							$my_down_uid[]=$m_v[\'uid\'];
						}
						if(in_array($v[\'uid\'],$my_down_uid)==false){
							'.$name.'[$k][\'photo\']=\'./\'.$config[\'member_logo\'];
						}
					}else{
						'.$name.'[$k][\'photo\']=\'./\'.$config[\'member_logo\'];
					}
				}
				if($config["user_name"]==3)
				{
					'.$name.'[$k]["username_n"] = "NO.".$v["id"];
				}elseif($config["user_name"]==2){
					if($userclass_name[$v[\'sex\']]==\'��\'){
						'.$name.'[$k]["username_n"] = mb_substr($v["username"],0,1,\'GBK\')."����";
					}else{
						'.$name.'[$k]["username_n"] = mb_substr($v["username"],0,1,\'GBK\')."Ůʿ";
					}
				}else{
					'.$name.'[$k]["username_n"] = $v["username"];
				}
				$a=date(\'Y\',strtotime('.$name.'[$k][\'birthday\']));
				'.$name.'[$k][\'age\']=date("Y")-$a;
				'.$name.'[$k][\'sex_n\']=$userclass_name[$v[\'sex\']];
				'.$name.'[$k][\'edu_n\']=$userclass_name[$v[\'edu\']];
				'.$name.'[$k][\'exp_n\']=$userclass_name[$v[\'exp\']];
				'.$name.'[$k][\'job_city_one\']=$city_name[$v[\'provinceid\']];
				'.$name.'[$k][\'job_city_two\']=$city_name[$v[\'cityid\']];
				'.$name.'[$k][\'job_city_three\']=$city_name[$v[\'three_cityid\']];
				'.$name.'[$k][\'salary_n\']=$userclass_name[$v[\'salary\']];
				'.$name.'[$k][\'report_n\']=$userclass_name[$v[\'report\']];
				'.$name.'[$k][\'type_n\']=$userclass_name[$v[\'type\']];
				'.$name.'[$k][\'lastupdate\']=date("Y-m-d",$v[\'lastupdate\']);
				//�������top����˵�������������а�ҳ��
				if($paramer[\'top\']){
					$m_name=$db->select_only($db_config[def]."member","`uid`=\'".$v[\'uid\']."\'","username");
					'.$name.'[$k][\'m_name\']=$m_name[0][\'username\'];
					'.$name.'[$k][\'user_url\']=Furl(array("url"=>"c:profile,id:".$v[\'uid\']));
				}else{
					'.$name.'[$k][\'user_url\']=Url("index","resume",array("id"=>$v[\'id\']),"1");
				}
				'.$name.'[$k]["hy_info"]=$industry_name[$v[\'hy\']];
				$job_classid=@explode(",",$v[\'job_classid\']);
				if(is_array($job_classid))
				{
					foreach($job_classid as $val)
					{
						$jobname[]=$job_name[$val];
					}
				}
				'.$name.'[$k][\'job_post\']=@implode(",",$jobname);
				//��ȡ����
				if($paramer[\'post_len\'])
				{
					$postname[$k]=@implode(",",$jobname);
					'.$name.'[$k][\'job_post_n\']=mb_substr($postname[$k],0,$paramer[post_len],"GBK");
				}
				if($paramer[\'keyword\'])
				{
					'.$name.'[$k][\'username\']=str_replace($paramer[\'keyword\'],"<font color=#FF6600 >".$paramer[\'keyword\']."</font>",$v[\'username\']);
					'.$name.'[$k][\'job_post\']=str_replace($paramer[\'keyword\'],"<font color=#FF6600 >".$paramer[\'keyword\']."</font>",'.$name.'[$k][\'job_post\']);
					'.$name.'[$k][\'job_post_n\']=str_replace($paramer[\'keyword\'],"<font color=#FF6600 >".$paramer[\'keyword\']."</font>",'.$name.'[$k][\'job_post_n\']);
					'.$name.'[$k][\'job_city_one\']=str_replace($paramer[\'keyword\'],"<font color=#FF6600 >".$paramer[\'keyword\']."</font>",$city_name[$v[\'provinceid\']]);
					'.$name.'[$k][\'job_city_two\']=str_replace($paramer[\'keyword\'],"<font color=#FF6600 >".$paramer[\'keyword\']."</font>",$city_name[$v[\'cityid\']]);
				}
				$jobname=array();
			}
			if($paramer[\'keyword\']!=""&&!empty('.$name.')){
				addkeywords(\'5\',$paramer[\'keyword\']);
			}
		}';
        //�Զ����ǩ END
        global $DiyTagOutputStr;
        $DiyTagOutputStr[]=$OutputStr;
        return SmartyOutputStr($this,$compiler,$_attr,'userlist',$name,'',$name);
    }
}
class Smarty_Internal_Compile_Userlistelse extends Smarty_Internal_CompileBase{
    public function compile($args, $compiler, $parameter){
        $_attr = $this->getAttributes($compiler, $args);

        list($openTag, $nocache, $item, $key) = $this->closeTag($compiler, array('userlist'));
        $this->openTag($compiler, 'userlistelse', array('userlistelse', $nocache, $item, $key));

        return "<?php }\nif (!\$_smarty_tpl->tpl_vars[$item]->_loop) {\n?>";
    }
}
class Smarty_Internal_Compile_Userlistclose extends Smarty_Internal_CompileBase{
    public function compile($args, $compiler, $parameter){
        $_attr = $this->getAttributes($compiler, $args);
        if ($compiler->nocache) {
            $compiler->tag_nocache = true;
        }

        list($openTag, $compiler->nocache, $item, $key) = $this->closeTag($compiler, array('userlist', 'userlistelse'));

        return "<?php } ?>";
    }
}
