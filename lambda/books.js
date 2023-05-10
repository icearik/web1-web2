const axios = require('axios');
const AWS = require('aws-sdk');
const dynamodb = new AWS.DynamoDB({ region: 'us-east-1' });

exports.handler = async (event) => {
   if (event.httpMethod === 'OPTIONS') {
        return {
          statusCode: 200,
          headers: {
            'Access-Control-Allow-Origin': '*',
            'Access-Control-Allow-Headers': '*',
            'Access-Control-Allow-Methods': 'OPTIONS,GET'
          }
        };
      }
  const eventHeaders = event.headers || {};
  const authToken = eventHeaders['x-auth-final-token']; 
  let headers = {
      'Access-Control-Allow-Origin': '*',
      'Access-Control-Allow-Headers': '*',
      'Access-Control-Allow-Methods': 'OPTIONS,GET'
  }
  if (!authToken) {
    return {
      statusCode: 401,
      headers: headers,
      body: JSON.stringify({ message: 'Missing authorization token'}),
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
  
  let key;
  
  // Check if key exists in query string parameters
  if (event.queryStringParameters !== null && event.queryStringParameters !== undefined) {
    if (event.queryStringParameters.key !== undefined 
      && event.queryStringParameters.key !== null 
      && event.queryStringParameters.key !== "") {
        key = event.queryStringParameters.key;
    }
  }

  // If key is missing, return 400 error response
  if (!key) {
    return {
      statusCode: 400,
      body: JSON.stringify({ message: "Missing 'key' parameter in request" }),
      headers: headers,
    };
  }
  const url = `https://openlibrary.org/authors/${key}/works.json?limit=100`;

  try {
    const response = await axios.get(url);
    return {
      statusCode: 200,
      headers: headers,
      body: JSON.stringify(response.data),
    };
  } catch (error) {
    console.error(error);
    return {
      statusCode: error.response.status,
      headers: headers,
      body: error.response.statusText
    };
  }
};

