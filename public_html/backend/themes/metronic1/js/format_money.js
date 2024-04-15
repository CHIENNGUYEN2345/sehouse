$(document).ready(function () {
    //Format tien vn bang dau ,
    //Input type = text
    $('input.number_price').each(function () {
        //tao  1 input phu, chua value that
        $(this).parent().append('<input type="text" class="value-number_price hidden" value="' + delChart($(this).val()) + '" name="' + $(this).attr('name') + '">');
        $(this).data('class', $(this).attr('name'));
        $(this).val(FormatNumber($(this).val()));
        $(this).removeAttr("name");
    });
    //Cap nhat value cho input phu viet nam
    $(document).on('blur', 'input.number_price', function () {
        var value = $(this).val();
        $(this).parent().find('.value-number_price').val(delChart(value));
        // if ($(this).parent().children().hasClass('value-price-cn')) {
        //     var value = $(this).parent().find('.col-md-5 input.form-control.cn').val();
        //     $(this).parent().find('.value-price-cn').val(delChart(value));
        // }
    });

    //Format tien te bang dau ,
    $(document).on('keyup', 'input.number_price', function () {
        var value = $(this).val();
        $(this).val(FormatNumber(value));
    });
    //Format tien te bang dau ,
    $(document).on('focus', 'input.number_price', function () {
        var value = $(this).val();
        $(this).val(FormatNumber(value));
    });

});
//xoa dau ,
function delChart(value) {
    var number = Math.floor(value.length / 3)
    var i;
    for (i = 0; i < number; i++) {
        value = value.replace(',', '');
    }
    return value;
}

var inputnumber = 'Giá trị nhập vào không phải là số';
//format tien te
function FormatNumber(str) {
    var strTemp = GetNumber(str);
    if (strTemp.length <= 3)
        return strTemp;
    strResult = "";
    for (var i = 0; i < strTemp.length; i++)
        strTemp = strTemp.replace(",", "");
    var m = strTemp.lastIndexOf(".");
    if (m == -1) {
        for (var i = strTemp.length; i >= 0; i--) {
            if (strResult.length > 0 && (strTemp.length - i - 1) % 3 == 0)
                strResult = "," + strResult;
            strResult = strTemp.substring(i, i + 1) + strResult;
        }
    } else {
        var strphannguyen = strTemp.substring(0, strTemp.lastIndexOf("."));
        var strphanthapphan = strTemp.substring(strTemp.lastIndexOf("."),
            strTemp.length);
        var tam = 0;
        for (var i = strphannguyen.length; i >= 0; i--) {

            if (strResult.length > 0 && tam == 4) {
                strResult = "," + strResult;
                tam = 1;
            }

            strResult = strphannguyen.substring(i, i + 1) + strResult;
            tam = tam + 1;
        }
        strResult = strResult + strphanthapphan;
    }
    return strResult;
}

function GetNumber(str) {
    var count = 0;
    for (var i = 0; i < str.length; i++) {
        var temp = str.substring(i, i + 1);
        if (!(temp == "," || temp == "." || (temp >= 0 && temp <= 9))) {
            alert(inputnumber);
            return str.substring(0, i);
        }
        if (temp == " ")
            return str.substring(0, i);
        if (temp == ".") {
            if (count > 0)
                return str.substring(0, ipubl_date);
            count++;
        }
    }
    return str;
}


Number.prototype.formatMoney = function (c, d, t) {
    var n = this,
        c = isNaN(c = Math.abs(c)) ? 2 : c,
        d = d == undefined ? "." : d,
        t = t == undefined ? "," : t,
        s = n < 0 ? "-" : "",
        i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
        j = (j = i.length) > 3 ? j % 3 : 0;
    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};
