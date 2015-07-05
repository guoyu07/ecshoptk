<?php
class Smarty_Internal_Compile_Ltjoblist extends Smarty_Internal_CompileBase{
    public $required_attributes = array('item');
    public $optional_attributes = array('name', 'key', 'limit', 'order', 'rebates', 't_len', 'hyclass', 'qw_hy', 'jobone', 'jobtwo', 'salary', 'uptime', 'provinceid', 'cityid', 'three_cityid', 'keyword', 'uid', 'order', 'jobtwo', 'rec','salary','ispage');
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
		$where = "`edate`>\'".time()."\' and `status`=\'1\' and `zp_status`<>\'1\' and `r_status`<>\'2\'";
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
			$where.=" AND (`com_name` like \'%".$paramer["keyword"]."%\' or `job_name` like \'%".$paramer["keyword"]."%\')";
		}
		//������ҵ����
		if($paramer["hyclass"]){
			$hyid=$lthy_type[$paramer["hyclass"]];
			foreach($hyid as $v)
			{
				$hyarr[]= "FIND_IN_SET(\'".$v."\',qw_hy)";
			}
			$hyarr=@implode(" or ",$hyarr);
			$where.=" AND ($hyarr)";
		}
		//������ҵ����
		if($paramer["qw_hy"]){
			$where.= " AND FIND_IN_SET(\'".$paramer["qw_hy"]."\',qw_hy)";
		}
		//ְλ����
		if($paramer["jobone"]){
			$where.=" AND `jobone`=\'".$paramer["jobone"]."\'";
		}
		//ְλ����
		if($paramer["jobtwo"]){
			$where.=" AND `jobtwo`=\'".$paramer["jobtwo"]."\'";
		}
		//��н
		if($paramer["salary"]){
			$where.=" AND `salary`=\'".$paramer["salary"]."\'";
		}
		//����ʱ��
		if($paramer["uptime"]){
			if($paramer["uptime"]>0){
				$time=time()-86400*30*$paramer["uptime"];
				$where.=" AND `lastupdate`>$time";
			}else{
				$time=time()-86400*30*12;
				$where.=" AND `lastupdate`<$time";
			}
		}
		//�Ƽ�
		if($paramer["rec"]){
			$where.=" AND `rec`=\'".$paramer["rec"]."\'";
		}
		//����
		if($paramer["provinceid"]){
			$where.=" AND `provinceid`=\'".$paramer["provinceid"]."\'";
		}
		if($paramer["cityid"]){
			$where.=" AND `cityid`=\'".$paramer["cityid"]."\'";
		}
		if($paramer["three_cityid"]){
			$where.=" AND `three_cityid`=\'".$paramer["three_cityid"]."\'";
		}
		//�û�uid
		if($paramer["uid"]){
			$where.=" AND `uid`=\'".$paramer["uid"]."\'";
		}
		if($paramer["rebates"]==\'1\'){
			$where.=" AND `rebates`<>\'\'";
		}
		//�����ֶΣ�Ĭ�ϰ���uid����
		if($paramer[order]){
			$where .= " ORDER BY $paramer[order]";
		}else{
			$where .= " ORDER BY  `lastupdate`  ";
		}
		//�������Ĭ�ϰ��տ�ʼʱ��������
		if($paramer["sort"]){
			$where .= " $paramer[sort]";
		}else{
			$where .= " DESC ";
		}
		if($paramer["limit"]){
			$limit= " limit $paramer[limit]";
		}
		if($paramer[ispage]){
			$limit = PageNav($paramer,$_GET,"lt_job",$where,$Purl,"","1",$_smarty_tpl);
            $_smarty_tpl->tpl_vars["firmurl"]=new Smarty_Variable;
			$_smarty_tpl->tpl_vars["firmurl"]->value = $config[\'sy_weburl\']."/lietou/index.php?c=post".$ParamerArr[firmurl];
		}
		'.$name.'=$db->select_all("lt_job",$where.$limit);
		if(!$paramer[ispage]){
            $_smarty_tpl->tpl_vars["firmurl"]=new Smarty_Variable;
			$_smarty_tpl->tpl_vars["t_count"]->value=count('.$name.');
		}
		if(is_array('.$name.')){
			foreach('.$name.' as $k=>$v){
				'.$name.'[$k] = $db->lt_array_action($v);
				//��job_name ��ȡ
				if(intval($paramer[\'t_len\'])>0)
				{
					$len = intval($paramer[\'t_len\']);
					'.$name.'[$k][\'job_name\'] = mb_substr($v[\'job_name\'],0,$len,"GBK");
				}
				if($v[\'usertype\']==3){
					'.$name.'[$k]["job_url"] = Lurl(array("url"=>"c:jobshow,id:".$v[\'id\']));
				}else{
					'.$name.'[$k]["job_url"] = Lurl(array("url"=>"c:jobcomshow,id:".$v[\'id\']));
				}
				'.$name.'[$k]["lastupdate"] = date("Y-m-d",$v["lastupdate"]);
				'.$name.'[$k]["edate"] = date("Y-m-d",$v["edate"]);
			}
		}
		if($paramer[\'keyword\']!=""&&!empty('.$name.')){
			addkeywords(\'7\',$paramer[\'keyword\']);
		}';
        //�Զ����ǩ END
        global $DiyTagOutputStr;
        $DiyTagOutputStr[]=$OutputStr;
        return SmartyOutputStr($this,$compiler,$_attr,'ltjoblist',$name,'',$name);
    }
}
class Smarty_Internal_Compile_Ltjoblistelse extends Smarty_Internal_CompileBase{
    public function compile($args, $compiler, $parameter){
        $_attr = $this->getAttributes($compiler, $args);

        list($openTag, $nocache, $item, $key) = $this->closeTag($compiler, array('ltjoblist'));
        $this->openTag($compiler, 'ltjoblistelse', array('ltjoblistelse', $nocache, $item, $key));

        return "<?php }\nif (!\$_smarty_tpl->tpl_vars[$item]->_loop) {\n?>";
    }
}
class Smarty_Internal_Compile_Ltjoblistclose extends Smarty_Internal_CompileBase{
    public function compile($args, $compiler, $parameter){
        $_attr = $this->getAttributes($compiler, $args);
        if ($compiler->nocache) {
            $compiler->tag_nocache = true;
        }

        list($openTag, $compiler->nocache, $item, $key) = $this->closeTag($compiler, array('ltjoblist', 'ltjoblistelse'));

        return "<?php } ?>";
    }
}
