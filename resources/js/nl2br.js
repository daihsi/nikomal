//textarea等で作成保存したデータの表示
//nl2br()の表示のような関数
export function nl2br(str) {
    str = str.replace(/\r\n/g, "<br />");
    str = str.replace(/(\n|\r)/g, "<br />");
    return str;
}