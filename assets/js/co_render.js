function parsecocode() {
    let obj = document.getElementsByTagName('cocode');
    for (let i = 0; i < obj.length; i++) {
        let tp = obj[i].getAttribute('type');
        let res = '<div class="alert alert-' + tp + '" role="alert"><span class="alert-inner--icon">';
        switch (tp) {
            case 'success':
                res += '<i class="fa fa-check-circle" aria-hidden="true"></i>';
                console.log("%c Cocode转译功能就绪!","color:green;font-weight:bold");
                break;
            case 'danger':
                res += '<i class="fa fa-exclamation-circle" aria-hidden="true"></i>';
                break;
            case 'warning':
                res += '<i class="fa fa-exclamation-circle" aria-hidden="true"></i>';
                break;
            case 'secondary':
                res += '<i class="fa fa-at" aria-hidden="true"></i>';
                break;
            default:
                res += '<i class="fa fa-info-circle" aria-hidden="true"></i>';
                break;
        }
        res += '</span><span class="alert-inner--text">' + obj[i].innerHTML + '</span></div>';
        obj[i].innerHTML = res;
    }
}

function parsecocard() {
    let obj = document.getElementsByTagName('cocard');
    for (let i = 0; i < obj.length; i++) {
        let front = obj[i].getAttribute('front');
        let back = obj[i].getAttribute('back');
        let name = obj[i].getAttribute('name');
        let bili = obj[i].getAttribute('bili');
        let card = '<div class="re-item"><dl class="re-item-front"><dt><img src="' + front + '"></dt><dd class="re-item-name">' + name + '</dd><dd class="re-item-des">' + obj[i].innerHTML + '</dd></dl><div class="re-item-back"><img src="' + back +'"></div><a href="https://space.bilibili.com/' + bili +'"><div class="button"><span class="text_button_bili">哔哩哔哩主页</span>		<svg><polyline class="o1_bili" points="0 0, 150 0, 150 55, 0 55, 0 0"></polyline><polyline class="o2" points="0 0, 150 0, 150 55, 0 55, 0 0"></polyline></svg></div></a></div>';
        obj[i].innerHTML = card;
    }
}

function parsecobili() {
    let obj = document.getElementsByTagName('cobili');
    for (let i = 0; i < obj.length; i++) {
        let vertical = obj[i].getAttribute('vertical');
        let aid = obj[i].getAttribute('aid');
        let cid = obj[i].getAttribute('cid');
        let ep = obj[i].getAttribute('ep');
        let bili_res = '<div style="position: relative; padding: 30% 45%;"><iframe style="position: absolute; width: 100%; height: 100%; left: 0; top: 0;"src="https://player.bilibili.com/player.html?aid='+ aid +'&cid=' + cid + '&page=' + ep + '&high_quality=1&danmaku=0';
        switch (vertical) {
            case '1':
                bili_res += '&as_wide=0"';
                break;
            default:
            bili_res += '&as_wide=1"';
                break;
        }
        bili_res += 'frameborder="no" scrolling="no"></iframe></div>';
        obj[i].innerHTML = bili_res;
    }
}