var endpoint = "api/profile/update.php";

/**
* These functions all return promises
* If you don't know what a promise is, you should git gud
**/

function changeProfilePicture(newProfilePicture) {
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
	
	});
}

function changePassword(newPassword) {
}