<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/datepicker.html.php';?>
<?php include '../../common/view/tablesorter.html.php';?>
<link rel="stylesheet" href="js/kindeditor/themes/default/default.css" />
<script src='js/kindeditor/kindeditor-min.js' type='text/javascript'></script>
<script src='js/kindeditor/lang/zh_CN.js' type='text/javascript'></script>

<style>
    #searchTab.active{
        background: #fff;
        background-color: rgb(255, 255, 255);
        background-image: none;
        background-repeat: repeat;
        background-attachment: scroll;
        background-clip: border-box;
        background-origin: padding-box;
        background-position-x: 0%;
        background-position-y: 0%;
        background-size: auto auto;
        padding: 2px 10px 3px;
        padding-bottom: 2px\0;
        border: 1px solid #ddd;
        border-bottom-width: 1px;
        border-bottom-style: solid;
        border-bottom-color: rgb(221, 221, 221);
        border-bottom: none;
    }
    body{
        width:100%;
        height:100%;
    }

</style>

<div id='featurebar'>
    <ul class='nav'>
        <?php
        echo '<li id="thisday">'.html::a(inlink('browse', "recTotal=0&recPerPage=".$recPerPage."&pageID=1&date=today&userId=".$userid),     '今天') . '</li>';
        echo '<li id="yesterday">'.html::a(inlink('browse', "recTotal=0&recPerPage=".$recPerPage."&pageID=1&date=yesterday&userId=".$userid), '昨天') . '</li>';
        echo '<li id="thisweek">'.html::a(inlink('browse', "recTotal=0&recPerPage=".$recPerPage."&pageID=1&date=thisweek&userId=".$userid),  '本周') . '</li>';
        echo '<li id="lastweek">'.html::a(inlink('browse', "recTotal=0&recPerPage=".$recPerPage."&pageID=1&date=lastweek&userId=".$userid),  '上周') . '</li>';
        echo '<li id="thismonth">'.html::a(inlink('browse', "recTotal=0&recPerPage=".$recPerPage."&pageID=1&date=thismonth&userId=".$userid), '本月') . '</li>';
        echo '<li id="lastmonth">'.html::a(inlink('browse', "recTotal=0&recPerPage=".$recPerPage."&pageID=1&date=lastmonth&userId=".$userid), '上月') . '</li>';
        echo '<li id="all">'.html::a(inlink('browse', "recTotal=0&recPerPage=".$recPerPage."&pageID=1&date=all&userId=".$userid),       '所有日志')   . '</li>';
        echo "<li id='$selectdate' class='input-group date'>".html::input('querydate',$date?$date:date('y-m-d',time()), "placeholder='搜索具体日期' class='form-date form-control' onchange=selectDate(this.value)") . '<span class="icon-calendar"></span> &nbsp;&nbsp;&nbsp;&nbsp;</li>';
        echo "<li class='w-150px'>" . html::select('account', $users,null, "class='form-control chosen' onchange=selectUser(this.value)") . '</li>';
        ?>
        <li id='searchTab' ><a href='javascript:;'><i class='icon-search icon'></i> 搜索</a></li>
    </ul>
    <div class='actions'>
        <a id="addlog" href="javascript:void(0)" class="btn "><i class="icon icon-plus"></i> 添加日志</a>
        <a id="exportLog" href="javascript:void(0)" class="btn "><i class="icon icon-download-alt"></i> 导出</a>
    </div>
</div>

<!--搜索框start-->
<form method="get" action="/index.php?m=worklog&f=browse"  id="searchform" class="form-condensed hide" >
    <div calss="row">
        <div  style="width:10%;min-width: 200px" class="input-group date form-date  pull-left" data-date="" data-date-format="dd MM yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
            <input id="startDate" name="startdate" class="form-control" size="16" type="text" value="<?php echo $startdate?>" readonly="" placeholder="开始日期">
            <span class="input-group-addon"><span class="icon-remove"></span></span>
            <span class="input-group-addon"><span class="icon-calendar"></span></span>
        </div>
        <div class="pull-left" style="margin-left:1%;line-height:200%">至</div>

        <div  style="width:10%;margin-left:1%; min-width: 200px" class="input-group date form-date pull-left" data-date="" data-date-format="dd MM yyyy" data-link-field="dtp_input2" data-link-format="yyyy-mm-dd">
            <input name="enddate" id="endDate" class="form-control" size="16" type="text" value="<?php echo $enddate?>" readonly="" placeholder="结束日期">
            <span class="input-group-addon"><span class="icon-remove"></span></span>
            <span class="input-group-addon"><span class="icon-calendar"></span></span>
        </div>
        <div class="input-group col-md-2 pull-left" style="margin-left:1%; min-width: 200px">
            <input name="keywork" id='keyWord' type="text" class="form-control" placeholder="内容关键字" value="<?php echo $keyword?>">
            <span class="input-group-btn">
                <button id='submitSearch' class="btn btn-default" type="button"><i class='icon-search icon'></i></button>
            </span>
        </div>
    </div>
    <br>
    <hr>
</form>
<!--搜索框end-->
<div class="col-xs-12" style="paddig:10px;">
    <table  class="col-xs-12  table datatable table-condensed table-hover table-striped tablesorter table-fixed table-selectable" >
        <thead>
        <tr>
            <th width="10%">用户姓名</th>
            <th width="10%" >记录时间</th>
            <th >发布内容</th>
            <th width="10%">标签</th>
            <th width="10%" sort=false>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($logs as $log){
            echo "<tr class='text-center' ondblclick='showDetail(this)' data-logid='".$log->id."'>";
            echo "<td data-userid='".$log->userId."'>".$log->realname."</td>" ;
            echo "<td data-createdtime='".date('Y-m-d H:i:s',$log->createdTime)."'>".$log->date." ".$log->time."</td>" ;
            echo "<td class='text-left' ><span>".strip_tags(preg_replace("/<img\s[^>]+>/",'[图片]',$log->content))."</span><span class='hidden'>".$log->content."</span></td>" ;
            echo "<td>".$log->tag."</td>" ;
            echo "<td>";
            echo "<a title='修改日志' log-id='".$log->id."' class='btn-icon' onclick='getDetail(this)'><i class=\"icon icon-pencil\"></i></a></tr>" ;
        }?>
        </tbody>
    </table>
    <div class="col-xs-12">
        <?php $pager->show('right');?>
    </div>
</div>


<div class="modal fade" style="position: absolute;height:100%" id="myLgModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">关闭</span></button>
                <h4 class="modal-title"><i class="icon icon-book"></i> <span id="modeltitle">添加</span>日志</h4>
            </div>
            <div class="modal-body">
                <div class="input-group" style="width:100%">
                    <span style="width:50%;float:left" class="input-group"  data-date="" data-link-format="yyyy-mm-dd">
                        <input class="form-control" size="16" type="text" value="" readonly="" placeholder="请选择日期" id="date" disabled>
                        <span class="input-group-addon"><span class="icon-remove"></span></span>
                        <span class="input-group-addon"><span disabled class="icon-calendar"></span></span>
                    </span>
                    <span style="width:50%;float:left" class="input-group date form-time"  data-date="" data-date-format="hh:ii" data-link-field="dtp_input3" data-link-format="hh:ii">
                        <input class="form-control" size="16" type="text" value="" readonly="" placeholder="请选择时间" id="time">
                        <span class="input-group-addon"><span class="icon-remove"></span></span>
                        <span class="input-group-addon"><span class="icon-time"></span></span>
                    </span>
                </div>
                <div style="width:100%;margin:20px 0">
                    <textarea style="width:100%;min-height:500px" name="content" class="form-control" placeholder="输入日志内容" id="content"></textarea>
                </div>
                <div class="input-group">
                    <span class="input-group-addon">标签:</span>
                    <input type="text" class="form-control" placeholder="可输入标签用于分类" id="tag">
                </div>
                <div class="list-group" style="display: none;position: absolute;left:80%" id="laboftag">
                    <?php foreach ($tags as $tag) {
                        if($tag){
                            echo '<a href="#" class="list-group-item" onclick="selectThis(this)">' .$tag. '</a>';
                        }
                        
                    }
                    ?>
                </div>

                <div class="input-group">
                    <div class="switch text-left">
                        <input type="checkbox" id="save" checked=false>
                        <label><button class="btn btn-success btn-mini">保存草稿</button><font size="2" color="#dcdcdc">(仅保留当前表单内容,刷新后不保存)</font></label>
                    </div>
                </div>
                <input type="hidden" id="logid" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary" id="submitLog">提交</button>
            </div>
        </div>
    </div>
</div>

<!--详情模态框-->
<div class="modal fade" id="detailModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">关闭</span></button>
                <h4 class="modal-title"><i class="icon icon-book"></i> 日志详情</h4>
            </div>
            <div class="modal-body" style="padding:0px 10px">
                <div style="width:100%;">
                    <span style="width:40%;  display:inline-block"><i class="icon icon-user"></i> <span id="showauthor"></span></span>
                    <span style="width:40%;  display:inline-block"><i class="icon icon-time"></i> <span id="showtime"></span></span>
                </div>
                <section class="content" id="showcontent" style="padding:5px;margin-top: 20px">
                </section>
                <br>
                <div id="showtag">

                </div>
            </div>
            <div class="modal-footer">
                <div style="float:left;color:grey"><br>创建于:<span id="showcreate"></span></div>
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>
<?php include '../../common/view/footer.html.php';?>
<script>

    //过滤的标签
    var htmlTags = {
        font : ['color', 'size', 'face', '.background-color'],
        span : [
            '.color', '.background-color', '.font-size', '.font-family', '.background',
            '.font-weight', '.font-style', '.text-decoration', '.vertical-align', '.line-height'
        ],
        div : [
            'align', '.border', '.margin', '.padding', '.text-align', '.color',
            '.background-color', '.font-size', '.font-family', '.font-weight', '.background',
            '.font-style', '.text-decoration', '.vertical-align', '.margin-left'
        ],
        table: [
            'border', 'cellspacing', 'cellpadding', 'width', 'height', 'align', 'bordercolor',
            '.padding', '.margin', '.border', 'bgcolor', '.text-align', '.color', '.background-color',
            '.font-size', '.font-family', '.font-weight', '.font-style', '.text-decoration', '.background',
            '.width', '.height', '.border-collapse'
        ],
        'td,th': [
            'align', 'valign', 'width', 'height', 'colspan', 'rowspan', 'bgcolor',
            '.text-align', '.color', '.background-color', '.font-size', '.font-family', '.font-weight',
            '.font-style', '.text-decoration', '.vertical-align', '.background', '.border'
        ],
        a : ['href', 'target', 'name'],
        embed : ['src', 'width', 'height', 'type', 'loop', 'autostart', 'quality', '.width', '.height', 'align', 'allowscriptaccess'],
        img : ['src', 'width', 'height', 'border', 'alt', 'title', 'align', '.width', '.height', '.border'],
        'p,ol,ul,li,blockquote,h1,h2,h3,h4,h5,h6' : [
            'align', '.text-align', '.color', '.background-color', '.font-size', '.font-family', '.background',
            '.font-weight', '.font-style', '.text-decoration', '.vertical-align', '.text-indent', '.margin-left'
        ],
        pre : ['class'],
        hr : ['class', '.page-break-after'],
        'br,tbody,tr,strong,b,sub,sup,em,i,u,strike,s,del' : []
    };
    var option = {
        afterUpload : function(url) {},
        htmlTags : htmlTags,
        uploadJson:"<?php echo $this->createLink('file', 'ajaxUpload', 'uid=' . uniqid('')) ;?>&dir=image",
        allowFileManager:true,

    }


    KindEditor.ready(function(K) {
        window.editor = K.create('#content',option);
    });

    //搜索框时间范围的提交
    $("#submitSearch").click(
        function(){
            var startdate = $("#startDate").val().replace('-','').replace('-','').replace(' ','');
            var enddate = $("#endDate").val().replace('-','').replace('-','').replace(' ','');
            var keyword = $("#keyWord").val();
            if(parseInt(startdate)>parseInt(enddate)){
                new $.zui.Messager('开始日期不能大于结束日期', {
                    type: 'danger'
                }).show();
                return false;
            }
            window.location.href="<?php echo $this->createLink('worklog','browse','recTotal=0&recPerPage=2&pageID=1&date=all&userId=')?>"+'&startdate='+startdate+'&enddate='+enddate+'&keyword='+keyword;
        }

    );

    //标签的轮训
    $("#tag").focus(
        function(){
            $("#laboftag").show();
        }
    );

    $("#tag").blur(
        function(){
            setTimeout(
                function(){
                    $("#laboftag").hide();
                },
                500
            );
        }
    );

    function selectThis(obj){
        var tag = $(obj).html();
        var nowtag = $("#tag").val();
        if(nowtag){
            $("#tag").val(nowtag+","+tag);
        }else{
            $("#tag").val(tag);
        }
        $(obj).hide();
        $("#laboftag").hide();
    }

    //搜索框的轮训
    $("#searchTab").click(
        function () {
            var tclass = $("#searchTab").attr('class');
            $("#searchTab").attr('class',(tclass == 'active')?'':'active');
            $("#searchform").toggle();
        }
    );


    //选择时间加粗
    if($('#<?php echo $selectdate?>')){
        $('#<?php echo $selectdate?>').css('font-weight','900');
    }

    //添加清除
    $("#addlog").click(

        function(){
            $('#laboftag a').show();
            var time = getTime();
            $('#logid').val('');
            $('#modeltitle').html("添加");
            $("#submitLog").attr("disabled",false);
            $('#time').attr("disabled",false).val(time);
            $('#tag').attr("disabled",false).val('');
            $('#save').attr("disabled",false);
            $("#title").html("添加");
            var save = $("#save").attr("checked") ;
            if(!save){
                editor.html('');;
                $("#tag").val('');
            }
            var date = getDate();
            $("#date").val(date);
            $("#submitLog").html("提交");
            $("#myLgModal").modal('show');
        }
    );

    //提交按钮
    $("#submitLog").click(function(){
        editlog();
    });

    //双击详情
    function bdlTr(obj){
        var tr = $(obj);
        tr.dblclick(
            function(){
                showDetail(this);
            }
        );
    }

    //提交日志
    function editlog(){

        var id = $("#logid").val() || 0;
        var addurl = "<?php echo $this->createLink('worklog','add') ?>";
        var editurl = "<?php echo $this->createLink('worklog','edit') ?>";
        var date = $("#date").val();
        var time = $("#time").val();
        var content = editor.html();
        var tag = $("#tag").val();
        $.ajax({
            url:(id == 0)? addurl:editurl,
            data:{date:date,time:time,content:content,tag:tag,id:id},
            type:'post',
            dataType:'json',
            async:false,
            success:function(data){
                if(data.code == 0){
                    new $.zui.Messager(data.msg, {
                        type: 'success'
                    }).show();
                    window.location.href=("<?php echo $this->createLink('worklog','browse')?>");
                }else{
                    new $.zui.Messager('提交失败:'+data.msg, {
                        type: 'warning'
                    }).show();
                    $("#submitLog").html("提交").attr("disabled",false);
                }
            },
            beforeSend:function(){
                $("#submitLog").html("提交中...").attr("disabled",true);
            }
        });

    }

    //获取详情
    function getDetail(obj,tr){

        $("#modeltitle").html("修改");
        var $this = $(obj) || null;
        var trs =  tr || $this.parents('tr');
        var create = trs.children().eq(1).attr("data-createdtime");
        var ctime = new Date(create).getTime();
        var ntime = new Date().getTime();
        if(ntime - ctime > 120000){
            new $.zui.Messager('提交超过两分钟的日志无法修改', {
                type: 'danger'
            }).show();
            showDetail(trs);
            return false;
        }
        var datearray = trs.children().eq(1).html().split(' ');
        var date = datearray[0];
        var time = datearray[1];
        var content = trs.children().eq(2).children().eq(1).html();
        console.log(content);
        var tag = trs.children().eq(3).html();
        var logid = trs.attr('data-logid');
        var user_id = trs.children().eq(0).attr('data-userid');
        if(user_id != '<?php echo $nowuser?>'){
            $("#submitLog").attr("disabled",true);
            $('#date').attr("disabled",true);
            $('#time').attr("disabled",true);
            $('#content').attr("disabled",true);
            $('#tag').attr("disabled",true);
            $('#save').attr("disabled",true);
        }

        $('#logid').val(logid);
        $('#date').val(date);
        $('#time').val(time);
        editor.html(content);
        $('#tag').val(tag);
        $("#myLgModal").modal('show');
    }



    //选择员工
    function selectUser(userId){
        window.location.href=("<?php echo $this->createLink('worklog','browse','recTotal=0&recPerPage=10&pageID=1&date='.str_replace([' ','-'],'',$getdate).'&userId=')?>"+userId);
    }

    //选择日期
    function selectDate(date){
        var sdate = date.replace("-",'').replace("-",'');
        window.location.href=("<?php echo $this->createLink('worklog','browse','recTotal=0&recPerPage=10&pageID=1')?>"+"&date="+sdate+"&userId=<?php echo $userid;?>");
    }

    //保持用户选择
    $("select option[value='<?php echo $userid?>']").attr("selected",'true');

    // 仅选择日期
    $(".form-date").datetimepicker(
        {
            language:  "zh-cn",
            weekStart: 1,
            todayBtn:  1,
            autoclose: 1,
            todayHighlight: 1,
            startView: 2,
            minView: 2,
            forceParse: 0,
            format: "yyyy-mm-dd",
            defaultDate:getDate()
        });

    //选择时间
    $(".form-time").datetimepicker({
        language:  "zh-cn",
        weekStart: 1,
        todayBtn:  1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 1,
        minView: 0,
        maxView: 1,
        forceParse: 0,
        format: 'hh:ii',
    });

    //详情查看
    function showDetail(obj){
        var trs =  $(obj);
        var time = trs.children().eq(1).html();
        var content = trs.children().eq(2).children().eq(1).html();
        console.log(content);
        var tag = trs.children().eq(3).html();
        var author = trs.children().eq(0).html();
        var create = trs.children().eq(1).attr("data-createdtime");

        var tags = tag.split(',');
        var taglab = '';
        for(var i in tags){
            taglab += '<a href="<?php echo $tagurl?>'+
                        tags[i]+'"><i class="icon icon-tag"></i>' +
                      '<span>'+tags[i]+'</span></a>';
        }

        
        $("#showtime").html(time);
        $("#showcontent").html(content);
        $("#showtag").html(taglab);
        $("#showauthor").html(author);
        $("#showcreate").html(create);
        $("#detailModal").modal('show');
    }

    $("#exportLog").click(
        function (){
            $.ajax({
                url:'<?php echo $this->createLink('worklog','export',"userid=$userid&date=$selectdate")?>',
                dataType:'json',
                type:'post',
                async:false,
                success:function(data){
                    if(data.code == 0 && data.data.length>0){
                        new $.zui.Messager('数据请求成功,正在生成EXCEL表格', {
                            type: 'success'
                        }).show();
                        var logs = data.data;
                        var tbody = '';

                        for(var i in logs){
                            var log = logs[i];
                            if(log){
                                tbody +="<tr><td>"+(1+parseInt(i))+"</td>"
                                    + "<td>"+log.realname+"</td>"
                                    + "<td>"+log.date+"</td>"
                                    + "<td>"+log.time+"</td>"
                                    + "<td>"+log.content+"</td>"
                                    + "<td>"+log.tag+"</td>"
                                    + "<td>"+timestampToTime(log.createTime)+"</td></tr>";
                            }
                        }

                        var table = "<table>" +
                            "<tr><th>序号</th>" +
                            "<th>姓名</th>" +
                            "<th>日期</th>" +
                            "<th>时间</th>"+
                            "<th>日志内容</th>"+
                            "<th>标签</th>"+
                            "<th>创建日期</th>"+
                            "</tr>" +tbody+
                            "</table>";

                        downexcel(table,'<?php echo $userid.'_'.$selectdate?>');

                    }else{
                        new $.zui.Messager('数据请求失败或无数据', {
                            type: 'danger'
                        }).show();
                    }
                },
                beforeSend:function(){
                    new $.zui.Messager('数据请求中...', {
                        type: 'info'
                    }).show();
                }
            });
        }
    );

    //导出表格
    function downexcel(table,name){
        // 使用outerHTML属性获取整个table元素的HTML代码（包括<table>标签），然后包装成一个完整的HTML文档，设置charset为urf-8以防止中文乱码
        var html = "<html><head><meta charset='utf-8' /></head><body>" + table + "</body></html>";
        // 实例化一个Blob对象，其构造函数的第一个参数是包含文件内容的数组，第二个参数是包含文件类型属性的对象
        var blob = new Blob([html], { type: "application/vnd.ms-excel" });
        // 利用URL.createObjectURL()方法为a元素生成blob URL
        var a = document.createElement("a");
        a.href =window.URL.createObjectURL(blob);
        a.download = name+".xlsx";
        document.body.appendChild(a);
        a.click();
    }

    //    时间戳转化
    function timestampToTime(timestamp) {
        var date = new Date(timestamp * 1000);//时间戳为10位需*1000，时间戳为13位的话不需乘1000
        Y = date.getFullYear() + '-';
        M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
        D = date.getDate() < 10 ? '0'+ date.getDate()  : date.getDate();
        D+= ' ';
        h = date.getHours() < 10 ?  '0' + date.getHours()+ ':' : date.getHours()+ ':';
        m = date.getMinutes() < 10 ? '0' + date.getMinutes() + ':' : date.getMinutes() + ':';
        s = date.getSeconds() < 10 ? '0' + date.getSeconds() : date.getSeconds();
        return Y+M+D+h+m+s;
    }


    //获取日期
    function getDate() {
        var date = new Date();//时间戳为10位需*1000，时间戳为13位的话不需乘1000
        Y = date.getFullYear() + '-';
        M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
        D = date.getDate() < 10 ? '0'+ date.getDate()  : date.getDate();
        return Y+M+D;
    }

    //获取时间
    function getTime() {
        var date = new Date();//时间戳为10位需*1000，时间戳为13位的话不需乘1000
        h = date.getHours() + ':';
        m = date.getMinutes() > 9 ? date.getMinutes() + ':' : '0'+date.getMinutes().toString() + ':';
        s = date.getSeconds() > 9 ? date.getSeconds() : '0'+date.getSeconds().toString();
        return h+m+s;
    }


    //获取本月
    function getThisMonth() {
        var date = new Date(<?php echo mktime(0,0,0,date('m'),1,date('Y'));?> * 1000);//时间戳为10位需*1000，时间戳为13位的话不需乘1000
        Y = date.getFullYear() + '-';
        M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
        D = date.getDate() + ' ';
        return Y+M+D;
    }

    var now = getDate();
    var start = $("#startDate").val();
    var end = $("#endDate").val();
    start == ''?$("#startDate").val(getThisMonth()):start;
    end == ''?$("#endDate").val(now):end;


    //控制outer的高度
    $(function(){
        var height = $("body").css('height');
        $('.outer').css("height",parseInt(height)+100+'px');
    })
</script>

