<?php
class Smarty_Internal_Compile_Key extends Smarty_Internal_CompileBase{
    public $required_attributes = array('item');
    public $optional_attributes = array('name', 'key', 'limit', 'recom', 'type', 'iswap', 'order', 'len');
    public $shorttag_order = array('from', 'item', 'key', 'name');
    public function compile($args, $compiler, $parameter){
        $_attr = $this->getAttributes($compiler, $args);

        $from = $_attr['from'];
        $item = $_attr['item'];
        if (!strncmp("\$_smarty_tpl->tpl_vars[$item]", $from, strlen($item) + 24)) {
            $compiler->trigger_template_error("item variable {$item} may not be the same variable as at 'from'", $compiler->lex->taglineno);
        }
        
        //�Զ����ǩ START
        $OutputStr='global $config;eval(\'$paramer='.str_replace('\'','\\\'',ArrayToString($_attr,true)).';\');$list=array();
		//�Ƿ��Ƽ�
		if($paramer[recom]){
			$tuijian = 1;
		}
		//���
		if($paramer[type]){
			$type = $paramer[type];
		}
		//��ѯ����
		if($paramer[limit]){
			$limit=$paramer[limit];
		}else{
			$limit=20;
		}
		include PLUS_PATH."/keyword.cache.php";
		if($paramer[iswap]){
			$wap = "/wap";
		}else{
			$index =1;
		}
		if(is_array($keyword)){
			if($paramer[iswap]){
				$i=0;
				foreach($keyword as $k=>$v){
					if($tuijian && $v[tuijian]!=1){
						continue;
					}
					if($type && $v[type]!=$type){
						continue;
					}
					$i++;
					if($v[type]=="1"){
						$v[url] = $config[sy_weburl].$wap."/index.php?m=once&keyword=".$v[key_name];
						$v[type_name]=\'һ�仰��Ƹ\';
					}elseif($v[type]=="3"){
						$v[url] = $config[sy_weburl].$wap."/index.php?m=com&keyword=".$v[key_name];
						$v[type_name]=\'ְλ\';
					}elseif($v[\'type\']=="4"){
						$v[\'url\'] = $config[sy_weburl].$wap."/index.php?m=firm&keyword=".$v[\'key_name\'];
						$v[\'type_name\']=\'��˾\';
					}elseif($v[\'type\']=="5"){
						$v[\'url\'] = $config[\'sy_weburl\'].$wap."/index.php?m=user&c=search&keyword=".$v[\'key_name\'];
						$v[\'type_name\']=\'�˲�\';
					}elseif($v[\'type\']=="6"){
						if($paramer[\'iswap\']){
							continue;
						}
						$v[\'url\'] = $config[\'sy_weburl\']."/lietou/index.php?c=service&keyword=".$v[\'key_name\'];
						$v[\'type_name\']=\'��ͷ\';
					}elseif($v[\'type\']=="7"){
						if($paramer[\'iswap\']){
							continue;
						}
						$v[\'url\'] = $config[\'sy_weburl\']."/lietou/index.php?c=post&keyword=".$v[\'key_name\'];
						$v[\'type_name\']=\'��ͷְλ\';
					}
					$v[\'key_title\']=$v[\'key_name\'];
					if($v[\'color\']){
						$v[\'key_name\']="<font color=\"".$v[\'color\']."\">".$v[\'key_name\']."</font>";
					}
					$list[] = $v;
					if($i==$limit){
						break;
					}
				}
			}else{
				$i=0;
				foreach($keyword as $k=>$v){
					if($tuijian && $v[\'tuijian\']!=1){
						continue;
					}
					if($type && $v[\'type\']!=$type){
						continue;
					}
					$i++;
					if($v[\'type\']=="1"){
						$v[\'url\'] = $config[\'sy_weburl\']."/index.php?m=once&keyword=".$v[\'key_name\'];
						$v[\'type_name\']=\'һ�仰��Ƹ\';
					}elseif($v[\'type\']=="3"){
						$v[\'url\'] = $config[\'sy_weburl\']."/index.php?m=com&c=search&keyword=".$v[\'key_name\'];
						$v[\'type_name\']=\'ְλ\';
					}elseif($v[\'type\']=="4"){
						$v[\'url\'] = $config[\'sy_weburl\']."/index.php?m=firm&keyword=".$v[\'key_name\'];
						$v[\'type_name\']=\'��˾\';
					}elseif($v[\'type\']=="5"){
						$v[\'url\'] = $config[\'sy_weburl\']."/index.php?m=user&c=search&keyword=".$v[\'key_name\'];
						$v[\'type_name\']=\'�˲�\';
					}elseif($v[\'type\']=="6"){
						if($paramer[\'iswap\']){
							continue;
						}
						$v[\'url\'] = $config[\'sy_weburl\']."/lietou/index.php?c=service&keyword=".$v[\'key_name\'];
						$v[\'type_name\']=\'��ͷ\';
					}elseif($v[\'type\']=="7"){
						if($paramer[\'iswap\']){
							continue;
						}
						$v[\'url\'] = $config[\'sy_weburl\']."/lietou/index.php?c=post&keyword=".$v[\'key_name\'];
						$v[\'type_name\']=\'��ͷְλ\';
					}
					$v[\'key_title\']=$v[\'key_name\'];
					if($v[\'color\']){
						$v[\'key_name\']="<font color=\"".$v[\'color\']."\">".$v[\'key_name\']."</font>";
					}
					$list[] = $v;
					if($i==$limit){
						break;
					}
				}
			}
		}';

        return SmartyOutputStr($this,$compiler,$_attr,'key','$list',$OutputStr,'$list');
    }
}
class Smarty_Internal_Compile_Keyelse extends Smarty_Internal_CompileBase{
    public function compile($args, $compiler, $parameter){
        $_attr = $this->getAttributes($compiler, $args);

        list($openTag, $nocache, $item, $key) = $this->closeTag($compiler, array('key'));
        $this->openTag($compiler, 'keyelse', array('keyelse', $nocache, $item, $key));

        return "<?php }\nif (!\$_smarty_tpl->tpl_vars[$item]->_loop) {\n?>";
    }
}
class Smarty_Internal_Compile_Keyclose extends Smarty_Internal_CompileBase{
    public function compile($args, $compiler, $parameter){
        $_attr = $this->getAttributes($compiler, $args);
        if ($compiler->nocache) {
            $compiler->tag_nocache = true;
        }

        list($openTag, $compiler->nocache, $item, $key) = $this->closeTag($compiler, array('key', 'keyelse'));

        return "<?php } ?>";
    }
}
