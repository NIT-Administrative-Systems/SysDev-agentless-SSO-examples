'use strict';

const axios = require('axios');
const querystring = require('querystring');

// Config
const webSSO = {
    loginBaseUrl: 'https://websso.it.northwestern.edu/amserver/UI/Login?goto=',
    checkTokenApi: 'https://websso.it.northwestern.edu/amserver/identity/attributes',
};

exports.handler = async (event, context, callback) => {
    // Get request and request headers
    const request = event.Records[0].cf.request;
    const cookie_jar = parseCookies(request.headers.cookie);

    // Figure out if they are authenticated.
    if (cookie_jar.openAMssoToken == null) {
        return callback(null, redirectToLogin(request));
    }

    var ssoResponse = null;
    try {
        ssoResponse = await axios.post(webSSO.checkTokenApi, querystring.stringify({
            'subjectid': cookie_jar.openAMssoToken,
            'attributenames': 'UserToken',
        }));
    } catch (error) {
        return callback(null, redirectToLogin(request));
    }

    const netid = getNetId(ssoResponse.data || '');
    if (netid == null) {
        return callback(null, redirectToLogin(request));
    }

    return callback(null, request);
};

function parseCookies (cookie_header) {
    if (cookie_header === undefined || cookie_header[0] === undefined || cookie_header[0].value === undefined) {
        return [];
    }

    var cookie_jar = {};
    const raw_cookies = cookie_header[0].value.split(';');
    for (var i=0; raw_cookies.length>i; i++) {
        let cookie = raw_cookies[i].split('=');
        let key = cookie[0].trim();
        let value = cookie[1].trim();

        cookie_jar[key] = value;
    }

    return cookie_jar;
} // end parseCookies

function redirectToLogin (request) {
    let redirectUrl = 'https://' + request.headers.host[0].value + request.uri;
    let url = webSSO.loginBaseUrl + encodeURIComponent(redirectUrl);

    return {
        status: '302',
        statusDescription: 'Found',
        headers: {
            location: [{
                key: 'Location',
                value: url,
            }],
        },
    };
}

function getNetId (payload) {
    var attr = payload.split('\n');
    var goal = 'userdetails.attribute.value=';

    for (var line in attr) {
        if (attr[line].indexOf(goal) === 0) {
            return attr[line].replace(goal, '');
        }
    }

    return null;
}
