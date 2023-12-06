// File: script.js

document.addEventListener('DOMContentLoaded', function () {
    // Example: Change the background color of the body
    document.body.style.backgroundColor = '#f0f0f0';

    // Example: Add a click event listener to a button
    var myButton = document.getElementById('myButton');
    if (myButton) {
        myButton.addEventListener('click', function () {
            alert('Button clicked!');

            // Example: Toggle a class on the body
            document.body.classList.toggle('button-clicked');
        });
    }

    // Example: Fetch data from an API and update the page
    var fetchDataButton = document.getElementById('fetchDataButton');
    if (fetchDataButton) {
        fetchDataButton.addEventListener('click', function () {
            fetch('https://api.example.com/data')
                .then(response => response.json())
                .then(data => {
                    console.log('Data from API:', data);

                    // Example: Display the fetched data on the page
                    var dataContainer = document.getElementById('dataContainer');
                    if (dataContainer) {
                        dataContainer.innerHTML = 'Fetched Data: ' + JSON.stringify(data);
                    }
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                });
        });
    }

    console.log('Script loaded successfully!');
});
