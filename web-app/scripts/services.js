const URL_MICROSERVICE_USERS = "http://157.245.121.183:5001";
// const URL_MICROSERVICE_ATTACKS = "http://157.245.121.183:5002";

const URL_MICROSERVICE_ATTACKS = "http://localhost:8001";

function httpRequest(url, httpVerb, body, res, err) {
    let xmlhttp = new XMLHttpRequest();
    xmlhttp.open(httpVerb, url);
    if (body) {
        xmlhttp.send(body);
    } else {
        xmlhttp.send();
    }
    xmlhttp.onreadystatechange = function () {
        if (this.readyState == 4) {
            if (this.status >= 200 && this.status < 300) {
                if (res) {
                    res({
                        status: this.status,
                        res: xmlhttp.responseText
                    });
                }
            } else if (this.status >= 400 && this.status < 600) {
                if (err) {
                    err({
                        status: this.status,
                        res: xmlhttp.responseText
                    });
                }
            }
        }
    }
}

function httpGET(url, res, err) {
    httpRequest(url, "GET", null, res, err); // cand fac get nu trimit vreun body
}

function httpPOST(url, obj, res, err) {
    httpRequest(url, "POST", obj, res, err);
}
