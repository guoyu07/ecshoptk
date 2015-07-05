<?php
class Smarty_Internal_Compile_Lietoulist extends Smarty_Internal_CompileBase{
    public $required_attributes = array('item');
    public $optional_attributes = array('name', 'key', 'rec', 'limit', 'keyword', 'hy', 'hyclass', 'job', 'jobclass', 'rzid', 'order', 'ispage');
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
        
        //�Զ����ǩ START
        $OutputStr='global $db,$db_config,$config;eval(\'$paramer='.str_replace('\'','\\\'',ArrayToString($_attr,true)).';\');'.$name.'=array();
		include PLUS_PATH."/ltjob.cache.php";
		include PLUS_PATH."/lthy.cache.php";
		//��������������ҹ����ҳ����
		$ParamerArr = GetSmarty($paramer,$_GET);
		$paramer = $ParamerArr[arr];
		$Purl =  $ParamerArr[purl];
		$where="`yyzz_status`=\'1\' and `r_status`<>\'2\'";
		//�Ƿ����ڷ�վ��
		if($config[sy_web_site]=="1"){
			if($_SESSION[cityid]>0 && $_SESSION[cityid]!=""){
				$paramer[cityid]=$_SESSION[cityid];
			}
			if($_SESSION[three_cityid]>0 && $_SESSION[three_cityid]!=""){
				$paramer[three_cityid] = $_SESSION[three_cityid];
			}
		}
		//�ؼ���
		if($paramer["keyword"]){
			$where1[]="`realname` LIKE \'%".$paramer[keyword]."%\'";
			foreach($ltjob_name as $k=>$v){
				if(strpos($v,$paramer[keyword])!==false){
					$jobid[]=$k;
				}
			}
			if(is_array($jobid)){
				foreach($jobid as $value){
					$class[]="FIND_IN_SET(\'".$value."\',job)";
				}
				$where1[]=@implode(" or ",$class);
			}
			foreach($lthy_name as $k=>$v){
				if(strpos($v,$paramer[keyword])!==false){
					$hyid[]=$k;
				}
			}
			if(is_array($hyid)){
				foreach($hyid as $value){
					$class[]="FIND_IN_SET(\'".$value."\',hy)";
				}
				$where1[]=@implode(" or ",$class);
			}
			$where.=" AND (".@implode(" or ",$where1).")";
		}
		//��֤ID
		if($paramer["rzid"]){
			$where.=" AND `rzid`=\'".$paramer["rzid"]."\'";
		}
		//�Ƽ�
		if($paramer["rec"]){
			$where.=" AND `rec`=\'".$paramer["rec"]."\'";
		}
		//�ó���ҵ����
		if($paramer["hyclass"]){
			$hyid=$lthy_type[$paramer["hyclass"]];
			foreach($hyid as $v){
				$hyarr[]= "FIND_IN_SET(\'".$v."\',hy)";
			}
			$hyarr=@implode(" or ",$hyarr);
			$where.=" AND ($hyarr)";
		}
		//����
		if($paramer["cityid"]){
			$where.= " AND `cityid`=$paramer[cityid]";
		}
		if($paramer["three_cityid"]){
			$where.= " AND `three_cityid`=$paramer[three_cityid]";
		}
		//�ó���ҵ����
		if($paramer["hy"]){
			$where.= " AND FIND_IN_SET(\'".$paramer["hy"]."\',hy)";
		}
		//�ó�ְλ����
		if($paramer["jobclass"]){
			$jobid=$ltjob_type[$paramer["jobclass"]];
			foreach($jobid as $v){
				$jobarr[]= "FIND_IN_SET(\'".$v."\',job)";
			}
			$jobarr=@implode(" or ",$jobarr);
			$where.=" AND ($jobarr)";
		}
		//�ó�ְλ����
		if($paramer["job"]){
			$where.= " AND FIND_IN_SET(\'".$paramer["job"]."\',job)";
		}
		//�����ֶΣ�Ĭ�ϰ���uid����
		if($paramer[order]){
			if($paramer[order]=="rztime"){
				$where .= " ORDER BY rz_time ";
			}else{
				$where .= " ORDER BY $paramer[order] ";
			}
		}else{
			$where .= " ORDER BY `uid` ";
		}
		//�������Ĭ�ϰ��տ�ʼʱ��������
		if($paramer["sort"]){
			$where .= " $paramer[sort]";
		}else{
			$where .= " DESC ";
		}
		if($paramer[ispage]){
			$limit = PageNav($paramer,$_GET,"lt_info",$where,$Purl,"",1,$_smarty_tpl);
		}
		'.$name.'=$db->select_all("lt_info",$where.$limit);
		$atn=$db->select_all("atn","`uid`=\'".$_COOKIE[uid]."\'");
		if(is_array('.$name.')){
			foreach('.$name.' as $k=>$v){
				if(is_array('.$name.')){
					foreach($atn as $val){
						if($v[uid]==$val[sc_uid]){
							'.$name.'[$k][atn]=1;
						}
					}
				}
				$uid[]=$v[uid];
			}
			$joblist=$db->select_all("lt_job","`status`=\'1\' and `edate`>\'".time()."\' and `uid` in (".@implode(",",$uid).") order by `lastupdate` desc");
			foreach('.$name.' as $k=>$v){
				$i=0;$job="";
				foreach($joblist as $val)
				{//��ͷ����ְλ
					if($v[uid]==$val[uid] && $i<3){
						$job_url = Lurl(array("url"=>"c:jobshow,id:".$val[id]));
						$job.="<a href=\'".$job_url."\'>".$val[job_name]."</a> ";
						$i++;
					}
				}
				'.$name.'[$k]["joblist"]=$job;
				$jobsc="";
				if($v[job]!=""){//�ó�ְλ
					$job=@explode(",",$v[job]);
					foreach($job as $val){
						$jobsc.=$ltjob_name[$val]." ";
					}
				}
				'.$name.'[$k]["job"]=$jobsc;
				$hy="";
				if($v[hy]!=""){//�ó���ҵ
					$hyarr=@explode(",",$v[hy]);
					foreach($hyarr as $val){
						$hy.=$lthy_name[$val]." ";
					}
				}
				'.$name.'[$k]["hy"]=$hy;
				'.$name.'[$k]["name_url"] = Lurl(array("url"=>"c:headhunter,uid:".$v[uid]));//��ͷ����
				if($v[\'photo\']!=""){
					'.$name.'[$k][\'photo\'] = str_replace("./",$config[\'sy_weburl\']."/",$v[\'photo\']);
				}else if($v[\'photo\']==\'\'){
					'.$name.'[$k][\'photo\'] = $config[\'sy_weburl\']."/".$config[\'sy_lt_icon\'];
				}
				if($v[\'photo_big\']!=""){
					'.$name.'[$k][\'photo_big\'] = str_replace("./",$config[\'sy_weburl\']."/",$v[\'photo_big\']);
				}else if($v[\'photo_big\']==\'\'){
					'.$name.'[$k][\'photo_big\'] = $config[\'sy_weburl\']."/".$config[\'sy_lt_icon\'];
				}
			}
		}
		if($paramer[keyword]!=""&&!empty('.$name.'))
		{
			addkeywords(\'6\',$paramer[keyword]);
		}';
        //�Զ����ǩ END
        global $DiyTagOutputStr;
        $DiyTagOutputStr[]=$OutputStr;
        return SmartyOutputStr($this,$compiler,$_attr,'lietoulist',$name,'',$name);
    }
}
class Smarty_Internal_Compile_Lietoulistelse extends Smarty_Internal_CompileBase{
    public function compile($args, $compiler, $parameter){
        $_attr = $this->getAttributes($compiler, $args);

        list($openTag, $nocache, $item, $key) = $this->closeTag($compiler, array('lietoulist'));
        $this->openTag($compiler, 'lietoulistelse', array('lietoulistelse', $nocache, $item, $key));

        return "<?php }\nif (!\$_smarty_tpl->tpl_vars[$item]->_loop) {\n?>";
    }
}
class Smarty_Internal_Compile_Lietoulistclose extends Smarty_Internal_CompileBase{
    public function compile($args, $compiler, $parameter){
        $_attr = $this->getAttributes($compiler, $args);
        if ($compiler->nocache) {
            $compiler->tag_nocache = true;
        }

        list($openTag, $compiler->nocache, $item, $key) = $this->closeTag($compiler, array('lietoulist', 'lietoulistelse'));

        return "<?php } ?>";
    }
}
