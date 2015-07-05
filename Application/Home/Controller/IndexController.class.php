<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        /*$ZhixiaCityTwoList=M('common_district')->where(array('level'=>2,'upid in (1,2,9,22)'))->select();
        foreach($ZhixiaCityTwoList as $k=>$v){
            $CIDList[]=$v['id'];
        }
        $CityThreeList=M('common_district')->where(array('level'=>3,'upid not in ('.implode(',',$CIDList).')'))->select();
        set_time_limit(0);
        foreach($CityThreeList as $k=>$v){
            //$name=str_replace(array('��','��'),'',$v['name']);
            $name=$v['name'];
            $KeyInfo=M('common_district')->where(array('`id` = '.$v['upid'].''))->find();
            $keyname=str_replace(array('��','��','ʡ','��'),'',$KeyInfo['name']);
            $Url='http://localhost/'.U('Mongo/Index/Index').'&name='.urlencode($name).'&keyname='.urlencode($keyname);
            //echo file_get_contents($Url,);die;
            //header('Location:'.$Url);die;
            if(function_exists('file_get_contents')){
                $content=file_get_contents($Url);
            }else if(function_exists('curl_init')){
                $ch = curl_init();
                $timeout = 5;
                curl_setopt($ch, CURLOPT_URL, $Url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                $content=curl_exec($ch);
                curl_close($ch);
            }else{
                $this->get_admin_msg($_SERVER['HTTP_REFERER'],"�뿪��CURLģ�����file_get_contents����");
            }
            //echo $content;die;
        }die;*/
        $categories_tree=$this->get_categories_tree();
        $this->assign('categories',      $categories_tree); // ������
        
        $navigator_list=$this->get_navigator($ctype, $catlist);
        $this->assign('navigator_list',$navigator_list);
        
        $category_list=$this->cat_list(0, 0, true,  2, false);
        $this->assign('category_list',$category_list);
        
        $promotion_info=get_promotion_info();
        $this->assign('promotion_info',  $promotion_info);
         
        $this->assign('shop_notice',     C('shop_notice'));
        $new_articles=index_get_new_articles();
        $this->assign('new_articles',   $new_articles);   // ��������
        
        $brand_list=get_brands();
        $this->assign('brand_list',      $brand_list);
        
        $this->display();
    }
    function get_categories_tree($cat_id = 0){
        if ($cat_id > 0){
            $parent_info=M('category')->field(array('parent_id'))->where(array('cat_id'=>$cat_id))->find();
            $parent_id = $parent_info['parent_id'];
        }else{
            $parent_id = 0;
        }

        /*
        �жϵ�ǰ������ȫ���Ƿ��ǵ׼����࣬
        �����ȡ���׼������ϼ����࣬
        �������ȡ��ǰ���༰���µ��ӷ���
         */
        $count_info=M('category')->field(array('parent_id'))->where(array('parent_id'=>$parent_id,'is_show'=>1))->count();
        if ($count_info || $parent_id == 0){
            /* ��ȡ��ǰ���༰���ӷ��� */            
            $children_list=M('category')->field(array('cat_id,cat_name','parent_id','is_show'))->where(array('parent_id'=>$parent_id,'is_show'=>1))->order('sort_order ASC, cat_id ASC')->select();

            foreach ($children_list AS $row){
                if ($row['is_show']){
                    $cat_arr[$row['cat_id']]['id']   = $row['cat_id'];
                    $cat_arr[$row['cat_id']]['name'] = $row['cat_name'];
                    $cat_arr[$row['cat_id']]['url']  = U('/Home/category/index/', array('cid' => $row['cat_id'],'cname'=> $row['cat_name']));

                    if (isset($row['cat_id']) != NULL){
                        $cat_arr[$row['cat_id']]['cat_id'] = $this->get_child_tree($row['cat_id']);
                    }
                }
            }
        }
        if(isset($cat_arr)){
            return $cat_arr;
        }
    }
    function get_child_tree($tree_id = 0){
        $three_arr = array();
        $count_info=M('category')->field(array('parent_id'))->where(array('parent_id'=>$tree_id,'is_show'=>1))->count();
        if ($count_info || $tree_id == 0){
            $children_list=M('category')->field(array('cat_id,cat_name','parent_id','is_show'))->where(array('parent_id'=>$tree_id,'is_show'=>1))->order('sort_order ASC, cat_id ASC')->select();

            foreach ($children_list AS $row){
                if ($row['is_show'])

                $three_arr[$row['cat_id']]['id']   = $row['cat_id'];
                $three_arr[$row['cat_id']]['name'] = $row['cat_name'];
                $three_arr[$row['cat_id']]['url']  = U('/Home/category/index/', array('cid' => $row['cat_id'],'cname'=> $row['cat_name']));

                if (isset($row['cat_id']) != NULL){
                    $three_arr[$row['cat_id']]['cat_id'] = $this->get_child_tree($row['cat_id']);

                }
            }
        }
        return $three_arr;
    }
    /**
     * ȡ���Զ��嵼�����б�
     * @param   string      $type    λ�ã���top��bottom��middle
     * @return  array         �б�
     */
    function get_navigator($ctype = '', $catlist = array()){
        $res = M('nav')->where(array('ifshow'=>1))->order('type, vieworder')->select();

        $cur_url = substr(strrchr($_SERVER['REQUEST_URI'],'/'),1);

        if (intval($GLOBALS['_CFG']['rewrite'])){
            if(strpos($cur_url, '-')){
                preg_match('/([a-z]*)-([0-9]*)/',$cur_url,$matches);
                $cur_url = $matches[1].'.php?id='.$matches[2];
            }
        }else{
            $cur_url = substr(strrchr($_SERVER['REQUEST_URI'],'/'),1);
        }

        $noindex = false;
        $active = 0;
        $navlist = array(
            'top' => array(),
            'middle' => array(),
            'bottom' => array()
        );
        foreach ($res as $k1=>$v1){
            $navlist[$v1['type']][] = array(
                'name'      =>  $v1['name'],
                'opennew'   =>  $v1['opennew'],
                'url'       =>  $v1['url'],
                'ctype'     =>  $v1['ctype'],
                'cid'       =>  $v1['cid'],
                );
        }

        /*�����Զ����Ƿ����currentPage*/
        foreach($navlist['middle'] as $k=>$v){
            $condition = empty($ctype) ? (strpos($cur_url, $v['url']) === 0) : (strpos($cur_url, $v['url']) === 0 && strlen($cur_url) == strlen($v['url']));
            if ($condition){
                $navlist['middle'][$k]['active'] = 1;
                $noindex = true;
                $active += 1;
            }
        }
        if(!empty($ctype) && $active < 1){
            foreach($catlist as $key => $val){
                foreach($navlist['middle'] as $k=>$v){
                    if(!empty($v['ctype']) && $v['ctype'] == $ctype && $v['cid'] == $val && $active < 1){
                        $navlist['middle'][$k]['active'] = 1;
                        $noindex = true;
                        $active += 1;
                    }
                }
            }
        }
        if ($noindex == false) {
            $navlist['config']['index'] = 1;
        }
        return $navlist;
    }
    /**
     * ���ָ�������µ��ӷ��������
     *
     * @access  public
     * @param   int     $cat_id     �����ID
     * @param   int     $selected   ��ǰѡ�з����ID
     * @param   boolean $re_type    ���ص�����: ֵΪ��ʱ���������б�,���򷵻�����
     * @param   int     $level      �޶����صļ�����Ϊ0ʱ�������м���
     * @param   int     $is_show_all ���Ϊtrue��ʾ���з��࣬���Ϊfalse���ز��ɼ����ࡣ
     * @return  mix
     */
    function cat_list($cat_id = 0, $selected = 0, $re_type = true, $level = 0, $is_show_all = true){
        static $res = NULL;
        if ($res === NULL){
            $data = read_static_cache('cat_pid_releate');
            if ($data === false){
                $res=M('category')->alias('c')->field('c.cat_id, c.cat_name, c.measure_unit, c.parent_id, c.is_show, c.show_in_nav, c.grade, c.sort_order, COUNT(s.cat_id) AS has_children')->join(C('DB_PREFIX').'category s ON s.parent_id=c.cat_id')->group('c.cat_id')->order('c.parent_id, c.sort_order ASC')->select();

                $res2=M('goods')->field('cat_id, COUNT(*) AS goods_num')->where(array('is_delete'=>0,'is_on_sale'=>1))->group('cat_id')->select();

                $res3=M('goods_cat')->alias('gc')->field('gc.cat_id, COUNT(*) AS goods_num')->join(C('DB_PREFIX').'goods g ON g.goods_id = gc.goods_id')->where(array('g.goods_id = gc.goods_id AND g.is_delete = 0 AND g.is_on_sale = 1'))->select();

                $newres = array();
                foreach($res2 as $k=>$v){
                    $newres[$v['cat_id']] = $v['goods_num'];
                    foreach($res3 as $ks=>$vs){
                        if($v['cat_id'] == $vs['cat_id']){
                            $newres[$v['cat_id']] = $v['goods_num'] + $vs['goods_num'];
                        }
                    }
                }

                foreach($res as $k=>$v){
                    $res[$k]['goods_num'] = !empty($newres[$v['cat_id']]) ? $newres[$v['cat_id']] : 0;
                }
                //���������󣬲����þ�̬���淽ʽ
                if (count($res) <= 1000){
                    write_static_cache('cat_pid_releate', $res);
                }
            }else{
                $res = $data;
            }
        }

        if (empty($res) == true){
            return $re_type ? '' : array();
        }

        $options = cat_options($cat_id, $res); // ���ָ�������µ��ӷ��������

        $children_level = 99999; //�����������Ľ���ɾ��
        if ($is_show_all == false){
            foreach ($options as $key => $val){
                if ($val['level'] > $children_level){
                    unset($options[$key]);
                }else{
                    if ($val['is_show'] == 0){
                        unset($options[$key]);
                        if ($children_level > $val['level']){
                            $children_level = $val['level']; //���һ�£������ӷ���Ҳ��ɾ��
                        }
                    }else{
                        $children_level = 99999; //�ָ���ʼֵ
                    }
                }
            }
        }

        /* ��ȡ��ָ������������ */
        if ($level > 0){
            if ($cat_id == 0){
                $end_level = $level;
            }else{
                $first_item = reset($options); // ��ȡ��һ��Ԫ��
                $end_level  = $first_item['level'] + $level;
            }

            /* ����levelС��end_level�Ĳ��� */
            foreach ($options AS $key => $val){
                if ($val['level'] >= $end_level){
                    unset($options[$key]);
                }
            }
        }

        if ($re_type == true){
            $select = '';
            foreach ($options AS $var){
                $select .= '<option value="' . $var['cat_id'] . '" ';
                $select .= ($selected == $var['cat_id']) ? "selected='ture'" : '';
                $select .= '>';
                if ($var['level'] > 0){
                    $select .= str_repeat('&nbsp;', $var['level'] * 4);
                }
                $select .= htmlspecialchars(addslashes($var['cat_name']), ENT_QUOTES) . '</option>';
            }
            return $select;
        }else{
            foreach ($options AS $key => $value){
                $options[$key]['url'] = build_uri('category', array('cid' => $value['cat_id']), $value['cat_name']);
            }
            return $options;
        }
    }
}