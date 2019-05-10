<?php
class worklog extends control
{   
	public function __construct(){
		parent::__construct();
    	$this->worklog->setMenu();
	}

    /**
     * Index
     * 日志首页
     * @access public
     * @return rederict
     */
    public function index()
    {
        $this->locate($this->createLink('worklog', 'browse'));
    }

    /**
     * browse
     * 日志首页
     * @access public
     * @param  int    $recTotal 数据总数
     * @param  int    $recPerPage 分页条数
     * @param  int    $pageID 页数
     * @param  string $date 日期查询类型
     * @param  int    $userId 查询用户id
     * @param  string $startdate 查询开始日期
     * @param  string $enddate 查询结束日期
     * @param  string $keyword 查询内容关键字
     * @return html
     */
    public function browse($recTotal=0,$recPerPage=10,$pageID=1,$date='all',$userId='',$startdate='',$enddate='',$keyword='',$tag='')
    {

        $userId = $this->get->userId ?  $this->get->userId: $this->session->user->id;
        $tags = $this->worklog->getTags($this->session->user->id);
        $users = $this->worklog->getUsers();
        if($this->get->keyword !='' || ($this->get->startdate !='' && $this->get->enddate !='') ){
            $keyword = $this->get->keyword ? $this->get->keyword :'';
            $startdate = $this->get->startdate;
            $enddate = $this->get->enddate;

            /* 加载分页类，并生成pager对象。*/
            $this->app->loadClass('pager', $static = true);
            $pager = new pager($recTotal, $recPerPage, $pageID);

            /* 将分页类传给model，进行分页。*/
            $logs = $this->worklog->getSearch($pager,$keyword,$startdate,$enddate);


            /* 赋值到模板。*/
            $this->view->keyword = $keyword;
            $this->view->tags = $tags;
            $this->view->logs = $logs;
            $this->view->users = $users;
            $this->view->pager = $pager;
            $this->view->userid = $userId;
            $this->view->date = ' ';
            $this->view->nowuser = $userId;
            $this->view->recPerPage = $recPerPage;
            $this->view->selectdate = 'all';
            $this->display();
            die;
        }


        $getdate = $this->get->date ? $this->get->date : 'all';
        $this->view->selectdate = $date;
        /* 加载分页类，并生成pager对象。*/
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);


        /* 将分页类传给model，进行分页。*/
        $logs = $this->worklog->getList($pager,$getdate,$userId,$tag);

        $tagurl = $this->createLink('worklog','browse',
            "recTotal=0"
            ."&recPerPage=0"
            ."&pageID=1"
            ."&date=".$date
            ."&userId=".$userId
            ."&startdate="
            ."&enddate="
            ."&keyword="
            ."&tag=".$tag
            );


        $date = strtotime($getdate);

        if($date){
            $date = date("Y-m-d",$date);
        }else{
            $date = " ";
        }
		
        /* 赋值到模板。*/
        $this->view->tagurl = $tagurl;
        $this->view->logs = $logs;
        $this->view->tags = $tags;
        $this->view->users = $users;
        $this->view->pager = $pager;
        $this->view->userid = $userId;
        $this->view->date = $date;
        $this->view->getdate = $getdate;
        $this->view->nowuser = $this->session->user->id;
        $this->view->recPerPage = $recPerPage;
        $this->display();
    }


    /**
     * add
     * 日志添加
     * @access public
     * @param string date 日志日期
     * @param string time 日志时间
     * @param string content 日志内容
     * @param string tag 日志标签
     * @return json 添加结果
     */
    public function add()
    {
        if(empty($_POST)){
            die(json_encode(['code' => 1,'msg' => "请求方式错误"]));
        }
        $data = [];
        $data['date'] = $this->post->date;
        $data['time'] = $this->post->time;
        $data['content'] = $this->post->content;
        $data['tag'] = str_replace(array("\r\n", "\r", "\n"), null,htmlspecialchars($this->post->tag) );
        $data['userId'] = (int)($this->session->user->id);
        $data['createdTime'] = time();

        if(mb_strlen($data['content'] > 500) ){
            die(json_encode(['code' => 1,'msg' => "内容长度不要超过500字"])) ;
        }

        if(mb_strlen($data['tag'] > 10) ){
            die(json_encode(['code' => 1,'msg' => "标签长度不要超过10个字"])) ;
        }

        $userId =  $this->session->user->id;
        $times = $this -> committime($userId);

        $today = date("Y-m-d",time());
        if( $data['date'] != $today ){
            die(json_encode(['code' => 1,'msg' => "只能提交今日日志"])) ;
        }
        if(in_array($data['time'],$times)){
            die(json_encode(['code' => 1,'msg' => "该日志已提交"])) ;
        }
        $result = $this->worklog->addlog($data);

        if($result === 1){
            echo json_encode(['code' => 0,'msg'=>'添加成功']);
        }else{
            echo json_encode(['code' => 1,'msg' => $result]);
        }

    }

     /**
     * edit
     * 日志修改
     * @access public
     * @param string date 日志日期
     * @param string time 日志时间
     * @param string content 日志内容
     * @param string tag 日志标签
     * @return json 修改结果
     */
    public function edit()
    {
        $id = $this->post->id;
        $createdTime = $this->worklog->getcreatedTime($id);
        $now = time();
        if($now - $createdTime > 120){
            die(json_encode(['code' => 1,'msg'=>'只能修改两分钟以内创建的日志']));
        }
        $data = [];
        $data['date'] = $this->post->date;
        $data['time'] = $this->post->time;
        $data['content'] = $this->post->content;
        $data['tag'] = htmlspecialchars($this->post->tag);

        $result = $this->worklog->editlog($data,$id);

        if($result !== false){
            echo json_encode(['code' => 0,'msg'=>'修改成功']);
        }else{
            echo json_encode(['code' => 1,'msg' => $result[0]]);
        }

    }

    /**
     * del
     * 日志删除
     * @access public
     * @param int id 日志id
     * @return json shanchu结果
     */
    public function del()
    {
        $id = $this->post->id;
        $result = $this->worklog->dellog($id);
        if($result  == 1){
            echo json_encode(['code' => 0,'msg'=>'删除成功']);
        }else{
            echo json_encode(['code' => 1,'msg' => $result[0]]);
        }

    }

    /**
     * export
     * 数据导出
     * @access public
     * @return array
     */
    public function export()
    {
        $userId = $this->get->userId ?  $this->get->userId: $this->session->user->id;
        $date = $this->get->date ? $this->get->date : 'all';

        $logs = $this->worklog->getExList($date,$userId);

        if($logs){
            echo json_encode(['code' => 0,'msg'=>'数据请求成功','data'=>$logs]);
        }else{
            echo json_encode(['code' => 1,'msg'=>'数据请求失败']);
        }
    }

    /**
     * committime
     * 数据导出
     * @param $userId 用户id
     * @access producted
     * @return array|boolean
     */
    protected function committime($userId)
    {

        $todaytimestamp = mktime(0,0,0,date("m"),date("d"),date("Y"));//获取今日时间戳
        $times = $this->worklog->getCommitTime($userId,$todaytimestamp);

        if($times){
            $data = [];
            foreach ($times as $time){
                $data[] = $time->time;
            }
            return  $data;
        }else{
            return false;
        }
    }
}

