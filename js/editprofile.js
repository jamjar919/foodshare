var endpoint = "api/profile/update.php";
// Note - you need the cookies library here.
/**
* These functions all return promises
* If you don't know what a promise is, you should git gud
**/

function uploadProfilePicture(file) {
        var imgurEndpoint = "https://api.imgur.com/3/image";
        var clientID = "dc753be426e679a"; // Is this secret???? not anymore l o l
        var fd = new FormData();
        fd.append('image', file);
        return new Promise(function(resolve,reject){
                if (file.type.match(/image/) && file.type !== 'image/svg+xml') {
                        $.ajax({
                                url: imgurEndpoint,
                                type:"POST",
                                headers: { 
                                        'Authorization' : 'Client-ID ' + clientID
                                },
                                data:fd,
                                processData: false,
                                contentType: false,
                        })  
                        .done(function(data) {
                                resolve(data);
                        })
                } else {
                        reject({"error":"Not an image file."});
                }
        });
}

function changeProfilePicture(newProfilePicture) {
        return new Promise(function(resolve, reject) {
                auth = Cookies.get('token');
		user = Cookies.get('username');
                $.post(endpoint, {token: auth, username: user, profilepicture: newProfilePicture})
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

function changePostcode(newPostcode) {
	return new Promise(function(resolve,reject) {
		// http://postcodes.io/
		// Also set our new location!
		auth = Cookies.get('token');
		user = Cookies.get('username');
		$.get("http://api.postcodes.io/postcodes/"+newPostcode)
		.done(function(data) {
			if (data.hasOwnProperty("error")) {
				reject(data);
			}
			theLocation = [data.result.latitude, data.result.longitude];
                        postcode = data.result.postcode;
			$.post(endpoint, {token: auth, username: user, postcode: postcode, location: theLocation})
			.done(function(data) {
				if (data.hasOwnProperty("error")) {
                                    reject(data);
				}
				resolve(data);
			})
			.fail(function(data) {
				reject(data);
			});
		})
		.fail(function(data) {
			reject(JSON.parse(data.responseText));
		});
	});
}

function changeEmail(newEmail) {
	return new Promise(function(resolve,reject) {
                auth = Cookies.get('token');
                user = Cookies.get('username');
                $.post(endpoint, {token: auth, username: user, email: newEmail})
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

function changePassword(newPassword) {
        return new Promise(function(resolve,reject) {
                auth = Cookies.get('token');
                user = Cookies.get('username');
                $.post(endpoint, {token: auth, username: user, password: newPassword})
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