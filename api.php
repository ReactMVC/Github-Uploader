<?php

class GitHub
{
    // Private properties to store the repository owner, repository name, and access token
    private $repo_owner;
    private $repo_name;
    private $access_token;

    // Constructor that takes the repository owner, repository name, and access token as arguments
    public function __construct($repo_owner, $repo_name, $access_token)
    {
        $this->repo_owner = $repo_owner;
        $this->repo_name = $repo_name;
        $this->access_token = $access_token;
    }

    // Method to post a file to the GitHub repository
    public function post($file_path)
    {
        // Generate a unique file name
        $file_name = uniqid() . '.jpg';

        // Construct the URL to the API endpoint for creating a new file
        $url = "https://api.github.com/repos/{$this->repo_owner}/{$this->repo_name}/contents/{$file_name}";

        // Define the data to be sent in the request body
        $data = array(
            "message" => "Add new file",
            "content" => base64_encode(file_get_contents($file_path)),
            "branch" => "main"
        );

        // Define the headers to be sent in the request
        $headers = array(
            "Content-Type: application/json",
            "Authorization: Bearer {$this->access_token}",
            "User-Agent: Mozilla/5.0 (X11; Linux x86_64; rv:109.0) Gecko/20100101 Firefox/113.0"
        );

        // Initialize a new cURL session
        $ch = curl_init();

        // Set the cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Execute the cURL request and store the result
        $result = curl_exec($ch);

        // Close the cURL session
        curl_close($ch);

        // Return the result
        return $result;
    }
}

// Create a new instance of the GitHub class with the repository owner, repository name, and access token
$uploader = new GitHub("github_username", "repository_name", "api_key");

// Check if a file was uploaded via a POST request
if (isset($_FILES['file'])) {
    // Get the path to the uploaded file
    $file_path = $_FILES['file']['tmp_name'];

    // Call the post method of the GitHub class to upload the file to the repository
    $result = $uploader->post($file_path);

    // Echo the result
    echo $result;
}