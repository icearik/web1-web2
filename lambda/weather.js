const axios = require('axios');
const AWS = require('aws-sdk');
const dynamodb = new AWS.DynamoDB({ region: 'us-east-1' });
exports.handler = async (event) => {
  let zipcode;
  const eventHeaders = event.headers || {};
  const authToken = eventHeaders['X-AUTH-FINAL-TOKEN']; 
  let headers = {'Access-Control-Allow-Origin': '*',
      'Access-Control-Allow-Headers': 'Content-Type',
      'Access-Control-Allow-Methods': 'OPTIONS,GET'}
  
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
  // Check if key exists in query string parameters
  if (event.queryStringParameters !== null && event.queryStringParameters !== undefined) {
    if (event.queryStringParameters.zipcode !== undefined 
      && event.queryStringParameters.zipcode !== null 
      && event.queryStringParameters.zipcode !== "") {
        zipcode = event.queryStringParameters.zipcode;
    }
  }

  // If key is missing, return 400 error response
  if (!zipcode) {
    return {
      statusCode: 400,
      headers: headers,
      body: JSON.stringify({ message: "Missing 'zipcode' parameter in request" })
    };
  }
  const url = `https://weather.visualcrossing.com/VisualCrossingWebServices/rest/services/timeline/${zipcode}?key=${process.env.API_KEY}`;

  try {
    const response = await axios.get(url);
    let temperature = response.data.days[0].temp;
    let conditions = response.data.days[0].conditions;
    return {
      statusCode: 200,
      headers: headers,
      body: JSON.stringify({temperature, conditions}),
    };
  } catch (error) {
    console.error(error);
    return {
      statusCode: error.response.status,
      headers: headers,
      body: error.response.data
    };
  }
};
