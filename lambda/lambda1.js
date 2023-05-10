// Ilyar Aisarov CSE451
const axios = require('axios');
const AWS = require('aws-sdk');
const dynamodb = new AWS.DynamoDB({ region: 'us-east-1' });
let count = 0;

exports.handler = async function handler(event, context) {
    let headers = {
        'Access-Control-Allow-Origin': '*',
        'Access-Control-Allow-Headers': '*',
        'Access-Control-Allow-Methods': 'OPTIONS,GET'
    }
    if (event.httpMethod === 'OPTIONS') {
        return {
            statusCode: 200,
            headers: headers
        };
    }
    const eventHeaders = event.headers || {};
    const authToken = eventHeaders['x-auth-final-token'];
    if (!authToken) {
        return {
            statusCode: 401,
            headers: headers,
            body: JSON.stringify({ message: 'Missing authorization token' }),
        };
    }
    const params = {
        TableName: 'tokens',
        Key: {
            'token': { S: authToken }
        }
    };
    try {
        const result = await dynamodb.getItem(params).promise();
        if (!result.Item) {
            return {
                statusCode: 401,
                headers: headers,
                body: JSON.stringify({ message: 'Invalid token' })
            };
        }

        const expirationTime = parseInt(result.Item.expirationTime.N);
        if (expirationTime < Math.floor(Date.now() / 1000)) {
            return {
                statusCode: 401,
                headers: headers,
                body: JSON.stringify({ message: 'Token expired, return to home page to get a new token' })
            };
        }

    } catch (err) {
        console.log(err);
        return {
            statusCode: 500,
            headers: headers,
            body: JSON.stringify({ message: 'Unexpected error' })
        };
    }
    const route = event.path;
    count++;
    if (route === '/calls') {
        return {
            statusCode: 200,
            headers: headers,
            body: JSON.stringify({ calls: count })
        };
    } else if (route === '/info') {
        return {
            statusCode: 200,
            headers: headers,
            body: JSON.stringify({
                message: 'To access the /api route, send a GET request to this Lambda function with the' +
                    ' path /prod/api?region={regionCode}, where regionCode is a country, subnational1, subnational2 or location code.' +
                    ' This should return the most recent bird observations'
            })
        };
    } else if (route === '/api') {
        let region;
        if (event.queryStringParameters !== null && event.queryStringParameters !== undefined) {
            if (event.queryStringParameters.region !== undefined
                && event.queryStringParameters.region !== null
                && event.queryStringParameters.region !== "") {
                region = event.queryStringParameters.region;
            }
        }

        // If key is missing, return 400 error response
        if (!region) {
            return {
                statusCode: 400,
                headers: headers,
                body: JSON.stringify({ message: "Missing 'region' parameter in request" })
            };
        }

        const token = process.env.API_KEY;

        const options = {
            headers: {
                'X-eBirdApiToken': token
            }
        };

        try {
            const response = await axios.get(`https://api.ebird.org/v2/data/obs/${region}/recent`, options);

            return {
                statusCode: response.status,
                headers: headers,
                body: JSON.stringify(response.data)
            };
        } catch (error) {
            console.error(error);
            if (error.response && error.response.status === 400) {
                return {
                    statusCode: 400,
                    headers: headers,
                    body: JSON.stringify({ message: "invalid region" })
                }
            } else {
                return {
                    statusCode: 500,
                    headers: headers,
                    body: JSON.stringify({ message: "Server Error" })
                }
            }
        }
    } else {
        return {
            statusCode: 404,
            headers: headers,
            body: JSON.stringify({ message: 'Route not found' })
        };
    }
}

