// 왼쪽에 있는 공백을 제거한다. 
function ltrim(value) {
    return value.replace(/^\s+/,"");
}
// 오른쪽에 있는 공백을 제거한다. 
function rtrim(value) {
    return value.replace(/\s+$/,"");
} 

function atrim(value){
    value = ltrim(value);
    value = rtrim(value);
    return value;
}

function loadingShowHide(){
    if ($("div.loading").length > 0){
        $("div.loading").remove();
    } else {
        $('body').append('<div class="loading">Loading&#8230;</div>');
    }
}

