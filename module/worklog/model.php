<?php
class worklogModel extends model
{
	public function setMenu()
	{

	}

	public function getCount(){
        $prefix = $this->dao->config->db->prefix;
	    $worklog = $this->dao
            ->select('id')
            ->from('zt_worklog')
            ->fetchAll();
        return count($worklog);
    }

    public function test(){
	    return 'this is worklog test!';
    }

    /**
     * 获取日志列表
     * @param $pager 分页对象
     * @param $date 日期
     * @param $userId 用户id
     * @param $tag 标签
     * @return lists 日志列表
     */
    public function getList($pager,$date,$userId,$tag)
    {
        $prefix = $this->dao->config->db->prefix;
        $lists =  $this->dao
            ->select('t1.*, t2.realname')
            ->from('zt_worklog')
            ->alias('t1')
            ->leftJoin($prefix.'user')
            ->alias('t2')
            ->on('t1.userId = t2.id ')
            ->where('1 = 1');
            switch($date){
                case 'today':
                    $lists = $lists  ->andWhere(' DATEDIFF( t1.date , NOW() ) = 0' );
                    break;
                case 'yesterday':
                    $lists = $lists  ->andWhere(' DATEDIFF( t1.date , NOW() ) = -1' );
                    break;
                case 'lastweek':
                    $lists = $lists  ->andWhere(' YEARWEEK(t1.date) = YEARWEEK(now()) - 1' );
                break;
                case 'thisweek':
                    $lists = $lists  ->andWhere(' YEARWEEK(t1.date) = YEARWEEK(now())' );
                    break;
                case 'lastmonth':
                    $lists = $lists  ->andWhere(' PERIOD_DIFF( date_format( now( ) , \'%Y%m\' ) , date_format(  t1.date, \'%Y%m\' ) ) = 1' );
                    break;
                case 'thismonth':
                    $lists = $lists  ->andWhere(' PERIOD_DIFF( date_format( now( ) , \'%Y%m\' ) , date_format(  t1.date, \'%Y%m\' ) ) = 0' );
                    break;
                case 'all':
                    break;
                default:
                    $formatdate = date("Y-m-d",strtotime($date));
                    $lists = $lists  ->andWhere(" t1.date = '".$formatdate."'" );
                    break;
            }

            if($userId != 'all'){
                $lists = $lists
                    ->andWhere(" t1.userId = ".$userId);
            }

           if($tag){
               $lists = $lists
                   ->andWhere(" t1.tag like '%".$tag."%'");
           }
                $lists = $lists
					->andWhere(" t2.dept > 0 ")
                    ->orderBy('t1.date desc,t1.time desc')
                    ->page($pager)
					->fetchAll();
            return $lists;
    }

    /**
     * 添加日志数据
     * @param array $data 日志信息
     * @return result 添加结果
     */
    public function addlog($data)
    {
        $prefix = $this->dao->config->db->prefix;
        $result = $this->dao->insert('zt_worklog')->data($data)
            ->autoCheck()
            ->batchCheck('userId, content ,date , time', 'notempty')
            ->exec();

        if(dao::isError()) return (array_values(dao::getError()));
        $add['objectType'] = 'worklog';
        $add['actor'] = $this->getUsername($data['userId'])->realname;
        $add['action'] = 'commitlog';
        $add['date'] = date("Y-m-d h:i:s",time());
        $this->dao->insert($prefix.'action')
            ->data($add)
//            ->printSQL();
            ->exec();
        return $result;
    }

    /**
     * 修改日志数据
     * @param array $data 日志信息
     * @param int $id 日志id
     * @return result 删除结果
     */
    public function editlog($data,$id)
    {
        $prefix = $this->dao->config->db->prefix;
        $result = $this->dao->update('zt_worklog')->data($data)
            ->where('id')->eq($id)
            ->andWhere('userId')->eq($this->session->user->id)
            ->autoCheck()
            ->batchCheck('content ,date , time', 'notempty')
            ->exec();

        if(dao::isError()) return (array_values(dao::getError()));

        return $result;
    }

    /**
     * 删除日志数据
     * @param array $id 日志id
     * @return result 删除结果
     */
    public function dellog($id)
    {
        $prefix = $this->dao->config->db->prefix;
        $result = $this->dao->delete()
            ->from('zt_worklog')
            ->where('id')->eq($id)
            ->andWhere('userId')->eq($this->session->user->id)
            ->exec();

        if(dao::isError()) return (array_values(dao::getError()));

        return $result;
    }


    /**
     * 获取用户列表
     * @return array 用户列表
     */
    public function getUsers()
    {
        $prefix = $this->dao->config->db->prefix;
        $result = $this->dao
            ->select('id,realname')
            ->from($prefix.'user')
            ->fetchAll();
        $userlist  = [];
        $userlist['all'] = '全部人员';
        foreach ($result as  $v){
            $userlist[$v->id] = $v->realname;
        }

        if(dao::isError()) return (array_values(dao::getError()));

        return $userlist;
    }

    /**
     * 获取创建时间
     * @param $id 日志id
     * @return int 创建时间
     */
    public function getcreatedTime($id)
    {
        $prefix = $this->dao->config->db->prefix;
        $result = $this->dao
            ->select('createdTime')
            ->from('zt_worklog')
            ->where('id')->eq($id)
            ->fetch();
        if(dao::isError()) return (array_values(dao::getError()));
        return $result->createdTime;
    }

    /**
     * 导出日志列表
     * @param $date 日期
     * @param $userId 用户id
     * @return lists 日志列表
     */
    public function getExList($date,$userId)
    {
        $prefix = $this->dao->config->db->prefix;
        $lists =  $this->dao
            ->select('t1.*, t2.realname')
            ->from('zt_worklog')
            ->alias('t1')
            ->leftJoin($prefix.'user')
            ->alias('t2')
            ->on('t1.userId = t2.id ');
        switch($date){
            case 'today':
                $lists = $lists  ->where(' DATEDIFF( t1.date , NOW() ) = 0' );
                break;
            case 'yesterday':
                $lists = $lists  ->where(' DATEDIFF( t1.date , NOW() ) = -1' );
                break;
            case 'lastweek':
                $lists = $lists  ->where(' YEARWEEK(t1.date) = YEARWEEK(now()) - 1' );
                break;
            case 'thisweek':
                $lists = $lists  ->where(' YEARWEEK(t1.date) = YEARWEEK(now())' );
                break;
            case 'lastmonth':
                $lists = $lists  ->where(' PERIOD_DIFF( date_format( now( ) , \'%Y%m\' ) , date_format(  t1.date, \'%Y%m\' ) ) = 1' );
                break;
            case 'thismonth':
                $lists = $lists  ->where(' PERIOD_DIFF( date_format( now( ) , \'%Y%m\' ) , date_format(  t1.date, \'%Y%m\' ) ) = 0' );
                break;
            case 'all':
                break;
            default:
                $formatdate = date("Y-m-d",strtotime($date));
                $lists = $lists  ->where(" t1.date = '".$formatdate."'" );
                break;
        }

        if($date == 'all'){
            $lists = $lists
                ->where( ' t1.userId = '.$userId);
        }else{
            $lists = $lists
                ->andWhere(' t1.userId = '.$userId);
        }
        $lists = $lists
            ->orderBy('t1.id DESC')
            ->fetchAll();
        if(dao::isError()) return (false);
        return $lists;
    }

    /**
     * 获取日志列表(搜索方法)
     * @param object $pager 分页对象
     * @param string $startdate 开始日期
     * @param string $enddate 结束日期
     * @param string $keyword 关键词
     * @return array lists 日志列表
     */
    public function getSearch($pager,$keyword,$startdate,$enddate)
    {
        $startdate = date("Y-m-d",strtotime($startdate));
        $enddate = date("Y-m-d",strtotime($enddate));
        $prefix = $this->dao->config->db->prefix;
        $lists =  $this->dao
            ->select('t1.*, t2.realname')
            ->from('zt_worklog')
            ->alias('t1')
            ->leftJoin($prefix.'user')
            ->alias('t2')
            ->on('t1.userId = t2.id ');
        

        if($startdate || $enddate){
                $lists = $lists->where( "t1.date between '".$startdate."' and '".$enddate."'")
                               ->andWhere("t1.content like '%".$keyword."%'");
        }else{
                $lists = $lists->where("t1.content like '%".$keyword."%'");
        }
        $lists = $lists
            ->orderBy('t1.id DESC')
            ->page($pager)
            // ->printSQL();
            ->fetchAll();
        return $lists;
    }

    /**
     * 获取用户标签(搜索方法)
     * @param int $userid 使用用户
     * @return array $tags 用户使用标签
     */
    public function getTags($userid)
    {
        $tags = [];
       $result = $this->dao
           ->select('tag')
           ->from('zt_worklog')
           ->where('userId')->eq($userid)
           ->groupBy('tag')
           ->fetchAll();
            foreach ($result as $value){
                    $tagsarr = explode(',',$value->tag);
                    foreach ($tagsarr as $tag){
                        $tags[] = $tag;
                    }
            }
            $tags = array_unique($tags);
       return $tags;
    }

    /**
     * 获取用户名
     * @param int $userid 用户id
     * @return stdclass realname 用户姓名查询结果
     */
    public function getUsername($userid)
    {
        $prefix = $this->dao->config->db->prefix;
        $result = $this->dao
            ->select('realname')
            ->from($prefix.'user')
            ->where('id')->eq($userid)
            ->fetch();
//            ->printSQL();

        if(dao::isError()) return (array_values(dao::getError()));

        return $result;
    }

    /**
     * 获取今日提交日志的时间戳
     * @param int $userid 用户id
     * @return stdclass times 用户今日提交的时间
     */
    public function getCommitTime($userid,$timestamp)
    {
        $result = $this->dao
            ->select('time')
            ->from('zt_worklog')
            ->where('userId')->eq($userid)
            ->andWhere('createdTime > '.$timestamp)
            ->fetchAll();
//            ->printSQL();
        if(dao::isError()) return (array_values(dao::getError()));
        return $result;
    }
}
