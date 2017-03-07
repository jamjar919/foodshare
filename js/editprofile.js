var endpoint = "api/profile/update.php";

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
			theLocation = [data.result.longitude, data.result.latitude];
			$.post(endpoint, {token: auth, username: user, postcode: newPostcode, location: theLocation})
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
}

function changePassword(newPassword) {
}