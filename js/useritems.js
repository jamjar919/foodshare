var endpoint = "api/profile/food.php"
function getUserItems(username,sort,num) {
    sort = sort || "Most recent";
    num = num || 10;
    return new Promise(function(resolve, reject) {
        $.get(endpoint, {username: username, sort: sort, num: num})
        .done(function(data) {
            if (data.hasOwnProperty("error")) {
                reject(data);
            }
            resolve(data);
        })
        .fail(function(data) {
            reject(data);
        });
    });
}
