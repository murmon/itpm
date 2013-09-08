var CLIENT_ID = '133676928039-3jc5bqa1jh4aji2utos8blams1hifqjs.apps.googleusercontent.com';
var apiKey = 'ge6XEBfUY2xR_aMp-EqhFmCG';
var scopes = 'https://www.googleapis.com/auth/plus.me';

var SCOPES = [
    'https://www.googleapis.com/auth/drive.file',
    'https://www.googleapis.com/auth/userinfo.email',
    'https://www.googleapis.com/auth/userinfo.profile',
    // Add other scopes needed by your application.
];

/**
 * Called when the client library is loaded.
 */
function handleClientLoad() {
    checkAuth();
}

/**
 * Check if the current user has authorized the application.
 */
function checkAuth() {
    gapi.auth.authorize(
        {'client_id': CLIENT_ID, 'scope': SCOPES.join(' '), 'immediate': true},
        handleAuthResult);
}

/**
 * Called when authorization server replies.
 *
 * @param {Object} authResult Authorization result.
 */
function handleAuthResult(authResult) {
    if (authResult) {
        // Access token has been successfully retrieved, requests can be sent to the API
    } else {
        // No access token could be retrieved, force the authorization flow.
        gapi.auth.authorize(
            {'client_id': CLIENT_ID, 'scope': SCOPES, 'immediate': false},
            handleAuthResult);
    }
}

setTimeout(function () {
    handleAuthResult();
    checkAuth();
    loadClient();
    retrieveAllFiles();
}, 700);


function loadClient(callback) {

    callback = function () {
        console.log('Loaded drive!');
    };

    gapi.client.load('drive', 'v2', callback);
}

function printFile(fileId) {
    var request = gapi.client.drive.files.get({
        'id': fileId
    });
    request.execute(function (resp) {
        if (!resp.error) {
            console.log('Title: ' + resp.title);
            console.log('Description: ' + resp.description);
            console.log('MIME type: ' + resp.mimeType);
        } else if (resp.error.code == 401) {
            // Access token might have expired.
            checkAuth();
        } else {
            console.log('An error occured: ' + resp.error.message);
        }
    });
}

var RESP;

function makeRequest() {

    var request = gapi.client.request({
        'path': '/drive/v2/files',
        'method': 'GET',
        //'q': 'sharedWithMe'
        //'params': {'maxResults': '1'}
    });

    request.execute(function (resp) {
        resp = RESP;
        console.log(111111111);
//        for (i = 0; i < resp.items.length; i++) {
//            var titulo = resp.items[i].title;
//            var fechaUpd = resp.items[i].modifiedDate;
//            var userUpd = resp.items[i].lastModifyingUserName;
//            var userEmbed = resp.items[i].embedLink;
//            var userAltLink = resp.items[i].alternateLink;
//
//            var fileInfo = document.createElement('li');
//            fileInfo.appendChild(document.createTextNode('TITLE: ' + titulo +
//                ' - LAST MODIF: ' + fechaUpd + ' - BY: ' + userUpd));
//            document.appendChild(fileInfo);
//        }
    });
}

function retrieveAllFilesInFolder(folderId, callback) {
    folderId = '0BxeetTONBzsZcnRoYlIzSzZLUk0';
    var retrievePageOfChildren = function(request, result) {
        request.execute(function(resp) {
            result = result.concat(resp.items);

            result.forEach(function(o){
                if (typeof o != 'undefined')
                    console.log(o.title);
            });

            var nextPageToken = resp.nextPageToken;
            if (nextPageToken) {
                request = gapi.client.drive.children.list({
                    'folderId' : folderId,
                    'pageToken': nextPageToken
                });
                retrievePageOfChildren(request, result);
            } else {

                result.forEach(function(o){
                    if (typeof o != 'undefined')
                        console.log(o.title);
                });

                callback(result);
            }
        });
    };
    var initialRequest = gapi.client.drive.children.list({
        'folderId' : '0BxeetTONBzsZcnRoYlIzSzZLUk0'
    });
    retrievePageOfChildren(initialRequest, []);
}

function log(e){
    console.log(e);
}

function retrieveAllFiles(callback) {
    var retrievePageOfFiles = function(request, result) {
        request.execute(function(resp) {
            result = result.concat(resp.items);

            debugger

            result.forEach(function(o){
                if (typeof o != 'undefined')
                    console.log(o.title);
            });


            var nextPageToken = resp.nextPageToken;
            if (nextPageToken) {
                request = gapi.client.drive.files.list({
                    'pageToken': nextPageToken
                });
                retrievePageOfFiles(request, result);
            } else {

                result.forEach(function(o){
                    if (typeof o != 'undefined')
                        console.log(o.title);
                });

                callback(result);
            }
        });
    };
    var initialRequest = gapi.client.drive.files.list({
        q: 'sharedWithMe and title contains "politech"'
    });
    retrievePageOfFiles(initialRequest, []);
}