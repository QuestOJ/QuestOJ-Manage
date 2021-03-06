function validateGroupname(str) {
	if (str.length == 0) {
		return '用户组名不能为空。';
	} else {
        return '';
    }
}

function validateComments(str) {
	if (str.length == 0) {
		return '备注不能为空。';
	} else {
        return '';
    }
}

function validateBlogID(str) {
	if (str.length == 0) {
		return '博客ID不能为空。';
	} else if (/\D/.test(str)) {
		return '博客ID应只包含0~9的数字。';
	} else {
        return '';
    }
}

function validateProblemID(str) {
	if (str.length == 0) {
		return '试题ID不能为空。';
	} else if (/\D/.test(str)) {
		return '试题ID应只包含0~9的数字。';
	} else {
        return '';
    }
}

function validateSiteName(str) {
	if (str.length == 0) {
		return '站点名称不能为空。';
	} else {
        return '';
    }
}

function validateSiteShortName(str) {
	if (str.length == 0) {
		return '站点简称不能为空。';
	} else {
        return '';
    }
}

function validateLoginTime(str) {
	if (str.length == 0) {
		return '登录有效期不能为空。';
	} else if (/\D/.test(str)) {
		return '登录有效期应只包含0~9的数字。';
	} else if (str < 300) {
		return '登录有效期应至少应为300秒。';
	} else {
        return '';
    }
}

function validateIP(str) {
	if (str.length == 0) {
		return '';
	} else if (! /^(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])$/.test(str)) {
		return 'IP格式不合法';
	} else {
		return '';
	}
}

function validateJudgername(str) {
	if (str.length == 0) {
		return '评测机名不能为空。';
	} else if (! /^[a-zA-Z0-9_]+$/.test(str)) {
		return '评测机名应只包含大小写英文字母、数字和下划线。';
	} else {
		return '';
	}
}

function IsURL(str_url){
    var strRegex = '^((https|http)?://)'
            + '(([0-9]{1,3}.){3}[0-9]{1,3}'
            + '|'
            + '([0-9a-z_!~*()-]+.)*'
            + '([0-9a-z][0-9a-z-]{0,61})?[0-9a-z].'
            + '[a-z]{2,6})'
            + '(:[0-9]{1,4})?'
            + '((/?)|'
            + '(/[0-9a-z_!~*().;?:@&=+$,%#-]+)+/?)/$';
    var re=new RegExp(strRegex);
    if (re.test(str_url)){
        return (true);
    }else{
        return (false);
    }
}

function validateSiteURL(str) {
	if (str.length == 0) {
		return '站点URL不能为空。';
	} else if (!IsURL(str)) {
		return 'URL不合法';
	} else {
		return '';
	}
}

function HTMLEncode(html) {
	var temp = document.createElement("div");
	(temp.textContent != null) ? (temp.textContent = html) : (temp.innerText = html);
	var output = temp.innerHTML;
	temp = null;
	return output;
}
	