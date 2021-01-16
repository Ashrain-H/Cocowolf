<?php function threadedComments($comments, $options) {
		$commentLevelClass = $comments->_levels > 0 ? ' comment-child' : ' comment-parent';
?>
 
<li id="li-<?php $comments->theId(); ?>">
	<div id="<?php $comments->theId(); ?>">
		<div  class="comment-item">
			<div class="<?php 
				if ($comments->_levels > 0) {
						echo 'comment-child';
				} else {
						echo 'comment-parent';
				}
			?>">
				<?php $comments->gravatar(80, ''); ?>
			</div>
			<div class="comment-body">
				<div class="comment-head">
					<img style="cursor: none;max-width: none;display: flex;margin: 0;margin-top: -10px;align-self: center;border-radius: unset;vertical-align: unset;border-style: none;position: absolute;border-radius:50%;" src="<?php getUserQQAvater($comments->mail) ?>" height="40" width="40" /><h5 style="margin-left: 50px;"><?php $comments->author(); ?> · <small><?php $comments->date('Y-m-d H:i'); ?></small><?php
					if ($comments->authorId) {
						if ($comments->authorId == $comments->ownerId){
							_e(' <span title="獸来也！站点管理员_(:зゝ∠)_" 
							class="badge badge-pill badge-success"><i class="fa fa-terminal" aria-hidden="true"></i>站点管理员</span>');
						}
					}
					?></h5>
				</div>
				<?php $comments->content(); ?>
				<div style="float: right;">
					<?php $comments->reply('<i class="fa fa-reply" aria-hidden="true"></i> 回复'); ?>
				</div>
			</div>
		</div>
	</div>
	<?php if ($comments->children) { ?>
	<div class="comment-children">
		<?php $comments->threadedComments($options); ?>
	</div>
	<?php } ?>
</li>
 
<?php } ?>

<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<section class="section">
	<div class="container" id="comments">
		<div class="content">
			<div class="row align-items-center justify-content-center">
				<h3><?php $this->commentsNum(_t('- 你很有想法,来说说看吧 -'), _t('- 仅有一条评论 -'), _t('- 已收到%d条评论 -')); ?></h3>
			</div>
			<?php $this->comments()->to($comments); ?>
			<?php if ($comments->have()): ?>
				<?php $comments->listComments(); ?>
				<div class="row align-items-center justify-content-center"><nav class="page-nav"><?php $comments->pageNav('<i class="fa fa-angle-left" aria-hidden="true"></i>', '<i class="fa fa-angle-right" aria-hidden="true"></i>', 1, '...', array('wrapTag' => 'ul', 'wrapClass' => 'pagination', 'textTag' => 'a', 'currentClass' => 'active', 'prevClass' => '', 'nextClass' => '')); ?></nav></div>
			<?php endif; ?>
			<div class="comment-card">
				<?php if($this->allow('comment')): ?>
				<?php if($this->user->hasLogin()):?>
				<?php
				$pg_this = new stdClass();
                $pg_this->_db = Typecho_Db::get();
                $username=$this->user->screenName;
				$row = $pg_this->_db->fetchRow($pg_this->_db->select('validate_state')->from('table.users')->where('screenName = ?', $username));
				if($row['validate_state']==="2"){
				    $checked=true;
				} else {
				    $checked=false;
				}
				?>
				<?php if($checked==true):?>
				<div id="<?php $this->respondId(); ?>" class="comment-reply">
					<div class="row align-items-center justify-content-center">
						<h3 id="response"><?php _e('发表评论'); ?></h3>
					</div>
					<div class="row align-items-center justify-content-center">
						<?php $comments->cancelReply(); ?>
					</div>
					<br/>
					<form method="post" action="<?php $this->commentUrl() ?>" id="comment-form" role="form" class="container" style="overflow: auto; zoom: 1;">
						<?php if($this->user->hasLogin()): ?>
						<p><?php _e('已登录为'); ?><a href="<?php $this->options->profileUrl(); ?>"><?php $this->user->screenName(); ?></a>。<a href="<?php $this->options->logoutUrl(); ?>" title="Logout"><?php _e('注销？'); ?></a></p>
						<?php else: ?>
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<div class="input-group mb-4">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fa fa-user-o" aria-hidden="true"></i></span>
										</div>
										<input type="text" name="author" id="author" class="form-control" placeholder="名称" value="<?php $this->remember('author'); ?>" required />
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<div class="input-group mb-4">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fa fa-envelope-o" aria-hidden="true"></i></span>
										</div>
										<input type="email" name="mail" id="mail" class="form-control" placeholder="邮箱" value="<?php $this->remember('mail'); ?>"<?php if ($this->options->commentsRequireMail): ?> required<?php endif; ?> />
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<div class="input-group mb-4">
										<div class="input-group-prepend">
											<span class="input-group-text"><i class="fa fa-globe" aria-hidden="true"></i></span>
										</div>
										<input type="url" name="url" id="url" class="form-control" placeholder="网站" value="<?php $this->remember('url'); ?>"<?php if ($this->options->commentsRequireURL): ?> required<?php endif; ?> />
									</div>
								</div>
							</div>
						</div>
						<?php endif; ?>
						<p>
							<textarea rows="8" cols="50" name="text" id="textarea" class="form-control" required ><?php $this->remember('text'); ?></textarea>
						</p>
						<p>
						<?php spam_protection_math();?>
							<button type="submit" class="btn btn-outline-success" id="add-comment-button" style="float: right;"><?php _e('提交评论'); ?></button>
						</p>
					</form>
				</div>
				<?php else: ?>
				<div class="row align-items-center justify-content-center"><h3 id="response"><h3 style="margin-bottom: 50px;">您必须验证邮箱才能发表评论<br><a href="/member.php">点击此处</a></a> 进入后台，验证邮箱。</h3></div>
				<?php endif; ?>
				<?php else: ?>
				<div class="row align-items-center justify-content-center"><h3 id="response"><h3 style="margin-bottom: 50px;">您必须 <a href="/member.php">登录</a> 才能发表评论</h3></div>
				<?php endif; ?>
				<?php else: ?>
				<div class="row align-items-center justify-content-center"><h3 id="response"><h3><?php _e('评论已关闭'); ?></h3></div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
<?php if($this->options->Pjax=="1"): ?>
<script>
	function focusToComment(username){
		var comments = $("#comments").children("div")
		getcomments = function(eles){
			var comlist = []
			var childrenlist = []
			for(var p = 0; p<eles.length; p++){
				var ele = $(eles[p])
				var lis = ele.children("ol").children("li")
				for(var j = 0; j<lis.length; j++){
					var h5 = $(lis[j]).children("div[id^='comment']").children(".comment-item").children(".comment-body").children(".comment-head").children("h5")
					var name
					for(var i = 0; i<h5.length; i++){
						name = h5[i].innerText
						name = $.trim(name.split("·")[0])
						if(name==username){
							comlist.push(parseInt($(lis[j]).attr("id").split("-")[2]))
						}
					}
					var child = $(lis[j]).children(".comment-children")
					if(child.length>0){
						childrenlist.push(child)
					}
				}
			}
			if(childrenlist.length>0){
				return comlist.concat(getcomments(childrenlist))
			}else{
				return comlist
			}
		}
		var commentIds = getcomments(comments)
		var commentId = Math.max(...commentIds)
		if(commentId!=-Infinity){
			$('html,body').animate({ scrollTop: $('#comment-'+commentId).offset().top-100}, 500)
			setTimeout(() => {
				$('#comment-'+commentId).fadeToggle(90);
				$('#comment-'+commentId).fadeToggle(110);
			}, 500);
		}
	}
	function bindsubmit(){
		$("#comment-form").submit(function() {
			var pgid = start_progress()
			$("#add-comment-button").attr("disabled",true)
			var data = $(this).serializeArray()
			var rubbish = <?php echo Typecho_Common::shuffleScriptVar(
            $this->security->getToken(clear_urlcan($this->request->getRequestUrl()))); ?>
            data.push({"name":"_","value":rubbish})
			$.ajax({
            url: $(this).attr("action"),
            type: $(this).attr("method"),
            data: data,
            complete: function(){
            	$("#add-comment-button").attr("disabled",false)
				stop_progress(pgid)
            },
            error: function() {
                alert("网络请求错误","请重新尝试提交评论")
            },
            success: function(html) {
            	var newdocument = new DOMParser().parseFromString(html, "text/html")
            	if(newdocument.title == "Error"){
            		var error = $.trim(newdocument.getElementsByClassName("container")[0].innerText)
            		alert("评论提交错误",error)
            	}else{
            		$("#comments").html(newdocument.getElementById("comments").innerHTML)
            		bindsubmit()
					var authorName = $("#author").val() ? $("#author").val() : $("a[href$='profile.php']").text()
					if(authorName){
						focusToComment(authorName)
					}
            	}
            }
        	})
        	return false;
		})
	}

	bindsubmit()
	var rubbishScripts = new DOMParser().parseFromString(`<?php echo $this->pluginHandle()->header("", $this);?>`, "text/html").getElementsByTagName("script")
	var script
	for(var i = 0; i<rubbishScripts.length; i++){
		script = rubbishScripts[i].innerHTML
		try {
			eval(script)
		} catch (error) {
			alert(error)
		}
	}
</script>

<script type="text/javascript">
    (function () {
    window.TypechoComment = {
        dom : function (id) {
            return document.getElementById(id);
        },
    
        create : function (tag, attr) {
            var el = document.createElement(tag);
        
            for (var key in attr) {
                el.setAttribute(key, attr[key]);
            }
        
            return el;
        },

        reply : function (cid, coid) {
            var comment = this.dom(cid), parent = comment.parentNode,
                response = this.dom('<?php $this->respondId() ?>'), input = this.dom('comment-parent'),
                form = 'form' == response.tagName ? response : response.getElementsByTagName('form')[0],
                textarea = response.getElementsByTagName('textarea')[0];

            if (null == input) {
                input = this.create('input', {
                    'type' : 'hidden',
                    'name' : 'parent',
                    'id'   : 'comment-parent'
                });

                form.appendChild(input);
            }

            input.setAttribute('value', coid);

            if (null == this.dom('comment-form-place-holder')) {
                var holder = this.create('div', {
                    'id' : 'comment-form-place-holder'
                });

                response.parentNode.insertBefore(holder, response);
            }

            comment.appendChild(response);
            this.dom('cancel-comment-reply-link').style.display = '';

            if (null != textarea && 'text' == textarea.name) {
                textarea.focus();
            }

            return false;
        },

        cancelReply : function () {
            var response = this.dom('<?php $this->respondId() ?>'),
            holder = this.dom('comment-form-place-holder'), input = this.dom('comment-parent');

            if (null != input) {
                input.parentNode.removeChild(input);
            }

            if (null == holder) {
                return true;
            }

            this.dom('cancel-comment-reply-link').style.display = 'none';
            holder.parentNode.insertBefore(response, holder);
            return false;
        }
    };
})();
</script>
<?php endif; ?>