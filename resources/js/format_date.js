//データ年月日フォーマット関数
export function formatDate(date, format) {

    // 年
    format = format.replace(/YYYY/g, date.getFullYear());

    //月
    format = format.replace(/MM/g, ('0' + (date.getMonth() + 1)).slice(-2));

    //日
    format = format.replace(/DD/g, ('0' + date.getDate()).slice(-2));

    //時
    format = format.replace(/hh/g, ('0' + date.getHours()).slice(-2));

    //分
    format = format.replace(/mm/g, ('0' + date.getMinutes()).slice(-2));

    //秒
    format = format.replace(/ss/g, ('0' + date.getSeconds()).slice(-2));
    return format;
}