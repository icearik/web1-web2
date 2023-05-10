const AWS = require('aws-sdk');
const dynamoDB = new AWS.DynamoDB.DocumentClient();

exports.handler = async (event) => {
  const headers = event.headers || {};
  const secretHeader = headers['AWS-Secret']; 
  const secretValue = process.env.SECRET;

  if (!secretHeader || secretHeader !== secretValue) {
    return {
      statusCode: 401,
      body: JSON.stringify({ message: 'Unauthorized'}),
      headers: {
        'Access-Control-Allow-Origin': '*',
        'Access-Control-Allow-Headers': 'Content-Type',
        'Access-Control-Allow-Methods': 'OPTIONS,GET'
      }
    };
  }

	if (event.body === null || event.body === undefined) {
	    return {
	        statusCode: 400,
	        body: JSON.stringify({message:"Invalid request body"})
	    }
	}
  let token;
  try {
    let contents = JSON.parse(event.body);
    token = contents.token;
  } catch (error) {
    return {
      statusCode: 400,
      body: JSON.stringify({ message: 'Invalid request body' }),
      headers: {
        'Access-Control-Allow-Origin': 'https://aisaroi.aws.csi.miamioh.edu',
        'Access-Control-Allow-Headers': 'Content-Type',
        'Access-Control-Allow-Methods': 'OPTIONS,GET'
      }
    };
  }

  // Store the token in DynamoDB with a TTL of 1 minute
  const params = {
    TableName: 'tokens', 
    Item: {
      token: token,
      expirationTime: Math.floor(Date.now() / 1000) + 60 // 
    }
  };

  try {
    await dynamoDB.put(params).promise();

    return {
      statusCode: 200,
      body: JSON.stringify({ message: 'Token stored successfully' }),
      headers: {
        'Access-Control-Allow-Origin': '*',
        'Access-Control-Allow-Headers': 'Content-Type',
        'Access-Control-Allow-Methods': 'OPTIONS,GET'
      }
    };
  } catch (error) {
    return {
      statusCode: 500,
      body: JSON.stringify({ message: 'Error storing token' }),
      headers: {
        'Access-Control-Allow-Origin': '*',
        'Access-Control-Allow-Headers': 'Content-Type',
        'Access-Control-Allow-Methods': 'OPTIONS,GET'
      }
    };
  }
};

