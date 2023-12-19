<?php
session_start();
include("config/database.php");
include("classes/user.php");

$database = new Database();
$authentification = new User($database);

$authentification->handleSignUp();
$authentification->handleSignIn();
?>

./


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>dataware | Authentification</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#7393B3] h-screen flex flex-col items-center justify-center">
<div >
    <a href="Dashboard.php" >
    <img src="./Images/Logo.png" class="w-40 mx-auto mb-10" alt="dataware Logo" />
    </a>
</div>

    <div class="w-full max-w-md bg-white p-8 rounded-lg shadow-md">

    <div class="flex justify-center mb-6 space-x-2">
            <button id="signInBtn" class="text-white px-6 py-2 bg-[#ff5349] hover:bg-[#ff5349] rounded-xl ">Sign In</button>
            <button id="signUpBtn" class=" text--[#ff5349] px-6 py-2 hover:underline border-2 rounded-xl border-[#ff5349]">Sign Up</button>
        </div>

        <form id="signInForm" action="#" method="POST" class="space-y-4">
            <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Sign in to your account</h1>

                <input type="email" name="email" id="email"
                    class="w-full p-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300"
                    placeholder="Your email" required>
          

            
                <input type="password" name="password" id="password"
                    class="w-full p-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300"
                    placeholder="Password" required>

            <button type="submit" name="submitSignIn"
                class="w-full bg-[#ff5349]  text-white p-2 rounded-md hover:bg-[#ff5349] focus:outline-none focus:ring focus:border-blue-300">
                Sign in
            </button>
        </form>

        <form id="signUpForm" action="#" method="POST" class="hidden space-y-4">
            <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">Create an account</h1>

            <input type="text" name="newname" id="newname"
                    class="w-full p-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300"
                    placeholder="Your name" required>
                <input type="email" name="newEmail" id="newEmail"
                    class="w-full p-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300"
                    placeholder="Your email" required>

            
                <input type="password" name="newPassword" id="newPassword"
                    class="w-full p-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300"
                    placeholder="New Password" required>
            
                    <input type="password" name="ConfirmPassword" id="ConfirmPassword"
                    class="w-full p-2 border rounded-md focus:outline-none focus:ring focus:border-blue-300"
                    placeholder="Confirm password" required>
            <button type="submit" name="submitSignUp"
                class="w-full bg-[#ff5349]  text-white p-2 rounded-md hover:bg-[#ff5349] focus:outline-none focus:ring focus:border-blue-300 mt-40">
                Sign up
            </button>
        </form>
    </div>


    <script>
        const signInBtn = document.getElementById('signInBtn');
        const signUpBtn = document.getElementById('signUpBtn');
        const signInForm = document.getElementById('signInForm');
        const signUpForm = document.getElementById('signUpForm');

        signInBtn.addEventListener('click', function () {
            signInForm.classList.remove('hidden');
            signUpForm.classList.add('hidden');
            signInBtn.classList.add('text-white','bg-[#ff5349]');
            signUpBtn.classList.remove('text-white','bg-[#ff5349]');
            signInBtn.classList.remove('text--[#ff5349]','hover:underline', 'border-2',  'border-[#ff5349]');
            signUpBtn.classList.add('text--[#ff5349]','hover:underline', 'border-2','border-[#ff5349]');
        });

        signUpBtn.addEventListener('click', function () {
            signInForm.classList.add('hidden');
            signUpForm.classList.remove('hidden');
            signInBtn.classList.remove('text-white','bg-[#ff5349]');
            signUpBtn.classList.add('text-white','bg-[#ff5349]');
            signInBtn.classList.add('text--[#ff5349]','hover:underline', 'border-2', 'border-[#ff5349]');
            signUpBtn.classList.remove('text--[#ff5349]','hover:underline', 'border-2',  'border-[#ff5349]');
        });
    </script>



</body>

</html>