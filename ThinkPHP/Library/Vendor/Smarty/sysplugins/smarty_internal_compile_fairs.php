<?php
class Smarty_Internal_Compile_Fairs extends Smarty_Internal_CompileBase{
    public $required_attributes = array('item');
    public $optional_attributes = array('name', 'key', 'len', 'limit', 'ispage');
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
        $OutputStr=''.$name.'=array();$time=time();eval(\'$paramer='.str_replace('\'','\\\'',ArrayToString($_attr,true)).';\');
		global $db,$db_config,$config;
		$ParamerArr = GetSmarty($paramer,$_GET);
		$paramer = $ParamerArr[arr];
		$Purl =  $ParamerArr[purl];
		$where = "1";
		$time = date("Y-m-d",time());
		//δ��ʼ
		if($paramer[state]==\'1\'){
			$where .=" AND `starttime`>$time";
		}elseif($paramer[state]==\'2\'){//������
			$where .=" AND `starttime`<$time AND `endtime`>$time";
		}elseif($paramer[state]==\'3\'){//�ѽ���
			$where .=" AND `endtime`<$time";
		}
		//�����ֶΣ�Ĭ�ϰ��տ�ʼʱ������
		if($paramer[order]){
			$where .= " ORDER BY $paramer[order] ";
		}else{
			$where .= " ORDER BY `starttime` ";
		}
		//�������Ĭ�ϰ��տ�ʼʱ��������
		if($paramer[sort]){
			$where .= " $paramer[sort]";
		}else{
			$where .= " DESC ";
		}
		//��ѯ����
		if($paramer[limit]){
			$limit=" LIMIT ".$paramer[limit];
		}else{
			$limit=" LIMIT 20";
		}
		if($paramer[ispage]){
			$limit = PageNav($paramer,$_GET,"zhaopinhui",$where,$Purl);
		}
		'.$name.'=$db->select_all("zhaopinhui",$where.$limit);
		if(is_array('.$name.')){
			foreach('.$name.' as $key=>$v){
				'.$name.'[$key]["stime"]=strtotime($v[starttime])-mktime();
				'.$name.'[$key]["etime"]=strtotime($v[endtime])-mktime();
				if($paramer[len]){
					'.$name.'[$key]["title"]=mb_substr($v[\'title\'],0,$paramer[len],"GBK");
				}
				'.$name.'[$key]["url"]=Url("index","zph",array("c"=>\'show\',"id"=>$v[\'id\']),"1");
			}
		}';
        //�Զ����ǩ END
        global $DiyTagOutputStr;
        $DiyTagOutputStr[]=$OutputStr;
        return SmartyOutputStr($this,$compiler,$_attr,'fairs',$name,'',$name);
    }
}
class Smarty_Internal_Compile_Fairselse extends Smarty_Internal_CompileBase{
    public function compile($args, $compiler, $parameter){
        $_attr = $this->getAttributes($compiler, $args);

        list($openTag, $nocache, $item, $key) = $this->closeTag($compiler, array('fairs'));
        $this->openTag($compiler, 'fairselse', array('fairselse', $nocache, $item, $key));

        return "<?php }\nif (!\$_smarty_tpl->tpl_vars[$item]->_loop) {\n?>";
    }
}
class Smarty_Internal_Compile_Fairsclose extends Smarty_Internal_CompileBase{
    public function compile($args, $compiler, $parameter){
        $_attr = $this->getAttributes($compiler, $args);
        if ($compiler->nocache) {
            $compiler->tag_nocache = true;
        }

        list($openTag, $compiler->nocache, $item, $key) = $this->closeTag($compiler, array('fairs', 'fairselse'));

        return "<?php } ?>";
    }
}
