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