<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
function themeConfig($form) {
	Typecho_Widget::widget('Widget_Themes_List')->to($themes);
	foreach ($themes -> stack as $key => $value){
		if($value["activated"]==1){
			break;
		}
	}
	
	if(!file_exists("themeupdater.php")){
		$updater = fopen("themeupdater.php", "w");
		$txt = '
		<html>
			<head>
				<title>Updater</title>
				<meta charset="UTF-8">
				<style>
					html {
						padding: 50px 10px;
						font-size: 16px;
						line-height: 1.4;
						color: #666;
						background: #F6F6F3;
						-webkit-text-size-adjust: 100%;
						-ms-text-size-adjust: 100%;
					}

					html,
					input { font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; }
					body {
						max-width: 500px;
						max-height: 30px;
						padding: 30px 20px;
						margin: 0 auto;
						background: #FFF;
					}
					ul {
						padding: 0 0 0 40px;
					}
					.container {
						max-width: 380px;
						_width: 380px;
						margin: 0 auto;
					}
				</style>
			</head>
			<body>
				<div class="container">
				<?php
				function getJsonRequest($url){
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					$output = curl_exec($ch);
					curl_close($ch);
					$output = json_decode($output,true);
					return $output;
				}
				function deldir($dir) {
					$dh=opendir($dir);
					while ($file=readdir($dh)) {
						if($file!="." && $file!="..") {
							$fullpath=$dir."/".$file;
							if(!is_dir($fullpath)) {
								unlink($fullpath);
							} else {
								deldir($fullpath);
							}
						}
					}
					closedir($dh);
					if(rmdir($dir)) {
						return true;
					} else {
						return false;
					}
				}
				function getRequest($url){
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					$output = curl_exec($ch);
					curl_close($ch);
					return $output;
				}
				$dir = "../usr/themes/Cocowolf";

				try{
					$version = getJsonRequest("https://data.jsdelivr.com/v1/package/resolve/gh/ashrain-h/Cocowolf")["version"];
					$files = getJsonRequest("https://data.jsdelivr.com/v1/package/gh/ashrain-h/Cocowolf@" . $version . "/flat")["files"];
					if(file_exists($dir)) deldir($dir);

					foreach ($files as $key => $value){
						$filecontent = getRequest("https://cdn.jsdelivr.net/gh/ashrain-h/Cocowolf@" . $version . "/" .$value["name"]);
						if (!file_exists(dirname($dir.$value["name"]))){
							mkdir(dirname($dir.$value["name"]),0755,true);
						}
						$fileobj = fopen($dir.$value["name"], "w");
						fwrite($fileobj, $filecontent);
						fclose($fileobj);
					}
					echo "主题更新成功！即将返回主题页面。";
					echo \'<meta http-equiv="refresh" content="3;url=themes.php">\';
					@unlink ("themeupdater.php");  
				}catch(Exception $e){
					echo "更新失败！请查看错误信息或者手动更新。<br>";
					echo $e;
				}
				?>
				</div>
			</body>
		</html>';
		fwrite($updater, $txt);
		fclose($updater);
	}
	
	echo '<script>
		var version = "' . $value["version"] . '"
		function toNum(a){
			var a=a.toString();
			var c=a.split('.');
			var num_place=["","0","00","000","0000"],r=num_place.reverse();
			for (var i=0;i<c.length;i++){ 
				var len=c[i].length;	   
				c[i]=r[len]+c[i];  
			} 
			var res = c.join(""); 
			return res; 
		} 
		
	</script>';
	
	echo 
	'
	<script src="https://cdn.jsdelivr.net/npm/jquery@3.4.1/dist/jquery.min.js"></script>
	<ul class="typecho-option typecho-option-submit">
		<li>
			<label class="typecho-label">
				Cocowolf主题更新
			</label>
		</li>
		<li>
			<p>
				<span class="description" id="update-dec" title="_(:зゝ∠)_这里没有彩蛋" class="badge badge-pill badge-success"><i class="fa fa-terminal" aria-hidden="true"></i>正在检测新版本</span>
			</p>
		</li>
		<li hidden id="update-btn-li">
			<button type="button" class="btn primary" id="update-btn">
			</button>
		</li>
	</ul>
	<script>
		$.ajax({
		url: "https://data.jsdelivr.com/v1/package/resolve/gh/ashrain-h/Cocowolf",
		dataType: "json",
		timeout: 30000,
		success: function(data) {
			var releaseVersion = data["version"]
			$("#update-btn-li").show()
			$("#update-dec").html("检测到新版本_(:Dゝ∠)_点击按钮更新")
			$("#update-btn").html("最新版本为" + releaseVersion + "，当前版本为" + version + "，" + (toNum(releaseVersion) > toNum(version) ? "你正在使用旧版本主题。点击更新" :  "你已更新至最新版本") +  (toNum(releaseVersion) < toNum(version) ? "...好家伙，比我版本都新" :  ""));
			if (toNum(releaseVersion) > toNum(version)) {
				$("#update-btn").click(function() {
					window.location.href = "themeupdater.php"
				});
			}
		},
		error: function() {
			$("#update-dec").html("检查更新程序出错_(xзゝ∠)_主题文件可能存在损坏");
		}
	});
	</script>';
	echo '
	<ul class="typecho-option typecho-option-submit">
		<li>
			<label class="typecho-label">
				设置用户称号
			</label>
		</li>
		<li>
			<p>
				<span class="description" title="_(:зゝ∠)_这里没有彩蛋" class="badge badge-pill badge-success">输入用户名和要设置的称号来设置用户称号</span>
			</p>
			<p>
				<input style="width: 150px" id="lorename" name="subtitle" type="text" class="text" placeholder="请输入目标用户名">
				<input style="width: 150px;margin-left: 15px;" id="loreuser" name="subtitle" type="text" class="text" placeholder="请输入要设置的称号">
				<button style="margin-left: 15px;" type="button" class="btn primary" id="changelore">修改称号</button>
				<button style="margin-left: 15px;" type="button" class="btn primary" id="removelore">删除称号</button>
			</p>
		</li>
		<li hidden id="statusli">
			<p id="statusinfo"></p>
		</li>
	</ul>
	<script>
	$("#changelore").click(function() {
	    var lorename = $("#lorename").val();
	    var loreuser = $("#loreuser").val();
		$.ajax({
		    type : "POST",
		    url: "/usr/plugins/CocowolfLore/Plugin.php?apitoken='.__TYPECHO_LORE_API_TOKEN__.'&action=changelore",
		    data: {
		        lname: lorename,
		        luser: loreuser
		    },
		    async:true,
		    success: function(rdata) {
		    	var status = rdata["status"]
		    	if(status==true){
		    	    $("#statusli").show();
		    	    $("#statusinfo").html("<font color=\"green\">"+rdata["message"]+"</font>");
		    	} else {
		    	    $("#statusli").show();
		    	    $("#statusinfo").html("<font color=\"red\">"+rdata["message"]+"</font>");
		    	}
		    },
		    error: function() {
		        $("#statusli").show();
		    	$("#statusinfo").html("<font color=\"red\">更新用户信息失败（无法连接到目标服务器）</font>");
		    }
	    })
	});
	</script>
	';
	$subtitle = new Typecho_Widget_Helper_Form_Element_Text('subtitle', NULL, '', _t('站点副标题'), _t('在这里填入站点副标题'));
	$form->addInput($subtitle);
	$logoUrl = new Typecho_Widget_Helper_Form_Element_Text('logoUrl', NULL, '', _t('站点 LOGO 地址'), _t('在这里填入一个图片 URL'));
	$form->addInput($logoUrl);
	$avatarUrl = new Typecho_Widget_Helper_Form_Element_Text('avatarUrl', NULL, '', _t('站点头像地址'), _t('在这里填入一个图片 URL,将会显示在主页正中间'));
	$form->addInput($avatarUrl);
	$indexImage = new Typecho_Widget_Helper_Form_Element_Text('indexImage', NULL, '', _t('首页背景图像地址'), _t('在这里填入一个图片 URL 地址, 以设定网站首页背景图片，留空则使用默认绿色渐变背景'));
	$form->addInput($indexImage);
	$randomImage = new Typecho_Widget_Helper_Form_Element_Textarea('randomImage', NULL, '', _t('随机背景图像地址'), _t('在这里填入一个或多个图片 URL 地址，每行一个，<strong>请勿包含多余字符</strong>，以设定网站文章页、独立页面以及其他页面的头图，设定后将随机显示，留空则使用默认绿色渐变背景'));
	$form->addInput($randomImage);
	$bubbleShow = new Typecho_Widget_Helper_Form_Element_Radio('bubbleShow', array('0' => _t('不显示'), '1' => _t('显示')), '1', _t('背景气泡'), _t('选择是否在首页以及文章页顶部背景处显示半透明气泡'));
	$form->addInput($bubbleShow);
	$footerText = new Typecho_Widget_Helper_Form_Element_Textarea('footerText', NULL, '兰德互娱<a href="https://developer.future-land.net/furry.html/" class="footer-link">Furry云赋能</a>技术支持 | Cocowolf主题 By 大以巴狼艾斯', _t('页脚左下角文字'), _t('在这里填入页脚左下角的说明文字，如 Copyright 和 备案信息，可添加 HTML 标签'));
	$form->addInput($footerText);
	$footerWidget = new Typecho_Widget_Helper_Form_Element_Radio('footerWidget', array('0' => _t('不显示'), '1' => _t('显示')), '1', _t('页脚小工具'), _t('选择是否在页面底部显示“最新评论”、“最新文章”等栏目'));
	$form->addInput($footerWidget);
	$customCss = new Typecho_Widget_Helper_Form_Element_Textarea('customCss', NULL, '', _t('自定义 css'), _t('在这里填入所需要的 css，以实现自定义页面样式，如调整字体大小等'));
	$form->addInput($customCss);
	$Pjax = new Typecho_Widget_Helper_Form_Element_Radio('Pjax', array('0' => _t('关闭'), '1' => _t('打开')), '1', _t('开启全站 pjax 模式'), _t('选择是否启用全站 pjax 模式提升用户访问体验。注意：启用该项可能带来页面加载问题'));
	$form->addInput($Pjax);
	$pjaxcomp = new Typecho_Widget_Helper_Form_Element_Textarea('pjaxcomp', NULL, '', _t('pjax 回调代码'), _t('在这里填入 pjax 渲染完毕后需执行的 JS 代码'));
	$form->addInput($pjaxcomp);
	$katex = new Typecho_Widget_Helper_Form_Element_Radio('katex', array('0' => _t('关闭'), '1' => _t('打开')), '0', _t('开启 katex 数学公式渲染'), _t('是否启用 katex 数学公式渲染'));
	$form->addInput($katex);
	$anim = new Typecho_Widget_Helper_Form_Element_Radio('anim', array('0' => _t('关闭'), '1' => _t('打开')), '0', _t('开启 Animate 动画渲染'), _t('是否启用增强动画<br>对渲染性能较差的浏览器请关闭'));
	$form->addInput($anim);
	$prismjs = new Typecho_Widget_Helper_Form_Element_Radio('prismjs', array('0' => _t('关闭'), '1' => _t('打开')), '0', _t('开启 prism.js 代码高亮'), _t('选择是否启用 prism.js 代码高亮'));
	$form->addInput($prismjs);
	$prismLine = new Typecho_Widget_Helper_Form_Element_Radio('prismLine', array('0' => _t('关闭'), '1' => _t('打开')), '0', _t('开启 prism.js 行号显示'), _t('选择是否显示 prism.js 代码高亮左侧行号'));
	$form->addInput($prismLine);
	$prismTheme = new Typecho_Widget_Helper_Form_Element_Select('prismTheme',
		array('prism' => _t('default'),
			'prism-coy' => _t('coy'),
			'prism-dark' => _t('dark'),
			'prism-funky' => _t('funky'),
			'prism-okaidia' => _t('okaidia'),
			'prism-solarizedlight' => _t('solarizedlight'),
			'prism-tomorrow' => _t('tomorrow'),
			'prism-twilight' => _t('twilight')
		),
	'prism', _t('prism.js 高亮主题'), _t('选择 prism.js 代码高亮的主题配色'));
	$form->addInput($prismTheme);
	$toc = new Typecho_Widget_Helper_Form_Element_Radio('toc',
		array('0' => _t('关闭'),
			'1' => _t('打开'),
		),
		'1', _t('开启 TOC 文章目录功能'), _t('选择是否开启 TOC 文章目录功能'));
	$form->addInput($toc);
	$toc_enable = new Typecho_Widget_Helper_Form_Element_Radio('toc_enable',
		array('0' => _t('关闭'),
			'1' => _t('展开'),
		),
		'0', _t('默认 TOC 目录展开状态'), _t('选择打开文章时 TOC 目录的展开状态'));
	$form->addInput($toc_enable);
	$counts = new Typecho_Widget_Helper_Form_Element_Radio('counts',
		array('0' => _t('关闭'),
			'1' => _t('开启'),
		),
		'1', _t('文章计数功能'), _t('这种症状持续多久了?'));
	$form->addInput($counts);
}

function printCategory($that, $icon = 0) { ?>
	<span class="list-tag">
		<?php if ($icon) { ?><i class="fa fa-folder-o" aria-hidden="true"></i><?php } ?>
		<?php foreach( $that->categories as $categories): ?>
		<a href="<?php print($categories['permalink']) ?>" class="badge badge-info badge-pill"><?php print($categories['name']) ?></a>
		<?php endforeach;?>
	</span>
<?php }

function printTag($that, $icon = 0) { ?>
	<span class="list-tag">
		<?php if ($icon) { ?><i class="fa fa-tags" aria-hidden="true"></i><?php } ?>
		<?php if (count($that->tags) > 0): ?>
			<?php foreach( $that->tags as $tags): ?>
			<a href="<?php print($tags['permalink']) ?>" class="badge badge-success badge-pill"><?php print($tags['name']) ?></a>
			<?php endforeach;?>
		<?php else: ?>
			<a class="badge badge-default badge-pill text-white">无标签</a>
		<?php endif;?>
	</span>
<?php }

function printAricle($that, $flag, $counts) { ?>
	<div class="card shadow content-card list-card <?php if ($flag): ?>content-card-head<?php endif; ?>">
		<section class="section">
			<div class="container animate__animated animate__fadeInUpBig">
				<div class="content">
					<h1><a class="text-default" href="<?php $that->permalink() ?>"><?php $that->title() ?></a></h1>
					<div class="list-object">
						<span class="list-tag"><i class="fa fa-calendar-o" aria-hidden="true"></i> <time datetime="<?php $that->date('c'); ?>"><?php $that->date();?></time></span>
						<span class="list-tag"><i class="fa fa-comments-o" aria-hidden="true"></i> <?php $that->commentsNum('%d');?> 条评论</span>
						<?php word_count($that->cid,$counts); ?>
					<!--php printCategory($that, 1); ?>-->
						<?php printTag($that, 1); ?>
						<span class="list-tag"><i class="fa fa-user-o" aria-hidden="true"></i> <a class="badge badge-warning badge-pill" href="<?php $that->author->permalink(); ?>"><?php $that->author();?></a></span>
					</div>
					<?php $that->content(''); ?>
					<br/>
					<div class="frame">
	<a href="<?php $that->permalink() ?>"><div class="button">
		
							<span class="text_button">详细内容</span>
					
		<svg>
		<polyline class="o1" points="0 0, 150 0, 150 55, 0 55, 0 0"></polyline>
		<polyline class="o2" points="0 0, 150 0, 150 55, 0 55, 0 0"></polyline>
	</svg>
	</div></a>
	
</div>
					
				</div>
			</div>
		</section>
	</div>
<?php }

function printToggleButton($that) {
	if ($that->getTotal() > $that->parameter->pageSize) { ?>
		<section class="section" style="padding-bottom: 1rem; padding-top: 6rem">
			<div class="container">
				<nav class="page-nav"><?php $that->pageNav('<i class="fa fa-angle-left" aria-hidden="true"></i>', '<i class="fa fa-angle-right" aria-hidden="true"></i>', 1, '...', array('wrapTag' => 'ul', 'wrapClass' => 'pagination justify-content-center', 'textTag' => 'a', 'currentClass' => 'active', 'prevClass' => '', 'nextClass' => '')); ?></nav>
			</div>
		</section>
	<?php }
}

function printBackground($url, $show) {
	_e('<div ');
	if ($url == '') _e('class="shape shape-style-1 shape-primary"');
	else _e('class="shape shape-style-1 shape-image" style="background-image: url(' . "$url" . ')"');
	_e('>');
	if ($show)
		_e('<span class="span-150"></span>
			<span class="span-50"></span>
			<span class="span-50"></span>
			<span class="span-75"></span>
			<span class="span-100"></span>
			<span class="span-75"></span>
			<span class="span-50"></span>
			<span class="span-100"></span>
			<span class="span-50"></span>
			<span class="span-100"></span>');
	_e('</div>');
}

function getRandomImage($str)
{
	if ($str == '') return '';
	$arr = explode(PHP_EOL, $str);
	return $arr[rand(0, sizeof($arr) - 1)];
}

function clear_urlcan($url)
{
	$rstr='';
	$tmparr=parse_url($url);
	$rstr=empty($tmparr['scheme'])?'http://':$tmparr['scheme'].'://';
	$rstr.=$tmparr['host'].$tmparr['path'];
	return $rstr;
}

function createCatalog($obj) {
	global $catalog;
	global $catalog_count;
	$catalog = array();
	$catalog_count = 0;
	$obj = preg_replace_callback('/<h([1-6])(.*?)>(.*?)<\/h\1>/i', function($obj) {
		global $catalog;
		global $catalog_count;
		$catalog_count ++;
		$catalog[] = array('text' => trim(strip_tags($obj[3])), 'depth' => $obj[1], 'count' => $catalog_count);
		return '<h'.$obj[1].$obj[2].'><a name="cl-'.$catalog_count.'"></a>'.$obj[3].'</h'.$obj[1].'>';
	}, $obj);
	return $obj;
}

function getCatalog() {
	global $catalog;
	$index = '';
	if ($catalog) {
		$index = '<ul>'."\n";
		$prev_depth = '';
		$to_depth = 0;
		foreach($catalog as $catalog_item) {
			$catalog_depth = $catalog_item['depth'];
			if ($prev_depth) {
				if ($catalog_depth == $prev_depth) {
					$index .= '</li>'."\n";
				} elseif ($catalog_depth > $prev_depth) {
					$to_depth++;
					$index .= '<ul>'."\n";
				} else {
					$to_depth2 = ($to_depth > ($prev_depth - $catalog_depth)) ? ($prev_depth - $catalog_depth) : $to_depth;
					if ($to_depth2) {
						for ($i=0; $i<$to_depth2; $i++) {
							$index .= '</li>'."\n".'</ul>'."\n";
							$to_depth--;
						}
					}
					$index .= '</li>';
				}
			}
			$index .= '<li><a name="dl-' . $catalog_item['count'] . '" href="javascript:jumpto('.$catalog_item['count'].')">'.$catalog_item['text'].'</a>';
			$prev_depth = $catalog_item['depth'];
		}
		for ($i=0; $i<=$to_depth; $i++) {
			$index .= '</li>'."\n".'</ul>'."\n";
		}
	}
	echo $index;
}

function antispm($comment){
$comment = spam_protection_pre($comment, $post, $result);
}
function spam_protection_math(){
    $num1=rand(1,22);
    $num2=rand(1,22);
    echo "<input type=\"text\" name=\"sum\" class=\"text\" value=\"\" size=\"25\" tabindex=\"4\" style=\"width:218px\" placeholder=\"请输入$num1+$num2 的计算结果\">\n";
    echo "<input type=\"hidden\" name=\"num1\" value=\"$num1\">\n";
    echo "<input type=\"hidden\" name=\"num2\" value=\"$num2\">";
}

function spam_protection_pre($comment, $post, $result){
    $sum=$_POST['sum'];
    switch($sum){
        case $_POST['num1']+$_POST['num2']:
        break;
        case null:
        throw new Typecho_Widget_Exception(_t('对不起: 请输入验证码。<a href="javascript:history.back(-1)">返回上一页</a>','评论失败'));
        break;
        default:
        throw new Typecho_Widget_Exception(_t('对不起: 验证码错误，请<a href="javascript:history.back(-1)">返回</a>重试。','评论失败'));
    }
    return $comment;
}

function themeInit($archive) {
	if ($archive->is('single')) {
		$archive->content = createCatalog($archive->content);
	}
}

function getUserQQAvater($email) {
    if($email) {
        if(strpos($email,'@qq.com') !==false) {
            $email=str_replace('@qq.com','',$email);echo '//q1.qlogo.cn/g?b=qq&nk='.$email.'&s=100';
        } else {
            $email= md5($email);echo '/usr/themes/Cocowolf/images/avatar.png';
        }
    } else {
        echo '/usr/themes/Cocowolf/images/avatar.png';
    }
}

function getUserLore($userid) {
    $user_db = Typecho_Db::get(); 
    $username = $user_db->fetchRow($user_db->select('name')->from('table.users')->where('uid = ?', $userid));
    $c_lusername = Typecho_Widget::widget('Widget_Options')->plugin('CocowolfLore')->lusername;
    $c_lorename = Typecho_Widget::widget('Widget_Options')->plugin('CocowolfLore')->lorename;
    $p_username = explode(',',$c_lusername);
    $p_lorename = explode(',',$c_lorename);
    for($i=0;$i<count($p_username);){
        if ($p_username[$i]==$username['name'] && $p_lorename[$i]!="0") {
            echo('<span id="badge" title="獸来也！专属荣誉称号.
岁月会冲淡回忆,但每一瞬的美好我们都一起记得.
颁发给为大家带来欢乐的小动物,以及人类." class="badge badge-pill badge-lore"><i class="fa fa-paw" style="width: 15px;height: 15px;position: relative;" aria-hidden="true"></i> '.$p_lorename[$i].'</span>');
            break;
        }
        $i++;
    }
}

function  word_count ($cid,$counts){
    if ($counts){
        $db=Typecho_Db::get ();
        $wc=$db->fetchRow ($db->select ('table.contents.text')->from ('table.contents')->where ('table.contents.cid=?',$cid)->order ('table.contents.cid',Typecho_Db::SORT_ASC)->limit (1));
        $text = preg_replace("/[^\x{4e00}-\x{9fa5}]/u", "", $wc['text']);
    _e('<span class="list-tag">共');
    _e(mb_strlen($text,'UTF-8'));
    _e('字</span>');
    }
	else{}
}