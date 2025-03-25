### README.md

---

# GymBridges

GymBridges is an integrated fitness platform designed to provide a comprehensive and convenient solution for fitness enthusiasts of all levels. The platform aims to centralize information about exercises, workout programs, nutrition, and gym locations, making it easier for users to achieve their fitness goals.

## Table of Contents

- [Introduction](#introduction)
- [Features](#features)
- [Installation](#installation)
- [Usage](#usage)
- [Contributing](#contributing)
- [License](#license)
- [Contact](#contact)

## Introduction

GymBridges is developed for individuals of various ages and fitness levels. It offers a unified resource that combines multiple functionalities into a single platform, providing users with easy access to information about exercises, training programs, nutrition, and gym locations.

### Target Audience

- Beginners starting their fitness journey
- Experienced athletes seeking new challenges and professional support
- Busy individuals looking to optimize their fitness experience
- Personal trainers aiming to offer services and attract clients

## Features

- **Muscle Selection Map:** Interactive map for selecting specific muscle groups and receiving relevant exercise information.
- **Google Maps Integration:** Provides information about nearby gyms in Riga using Google Maps API.
- **Exercise Library:** Categorized exercises with photos, videos, and descriptions.
- **Training Programs:** Various training programs and plans for different goals and fitness levels.
- **Personalized Workout Calculator:** Calculates personalized workout intensity based on individual characteristics and needs.
- **Nutrition Calculator:** Develops diet plans considering the user's nutritional requirements and goals.
- **User Authentication:** User registration and login functionality with data stored in a database.
- **Personal Account:** User profiles with customizable avatars, nicknames, and point systems.
- **Goals and Achievements:** Allows users to set and track fitness goals and achievements.
- **To-Do List:** Manage training and diet tasks and lists.

## Installation

### Prerequisites

- XAMPP (Apache, MySQL, PHP)
- Composer (for PHP dependencies)
- Node.js and npm (for frontend dependencies)

### Steps

1. **Clone the Repository**

    ```sh
    git clone https://github.com/yourusername/GymBridges.git
    cd GymBridges
    ```

2. **Set Up the Database**

    - Start XAMPP and enable Apache and MySQL.
    - Create a new database named `gymbridges`.
    - Import the `gymbridges.sql` file located in the `database` folder into the newly created database.

3. **Configure Environment Variables**

    Rename the `.env.example` file to `.env` and update the following variables:

    ```plaintext
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=gymbridges
    DB_USERNAME=root
    DB_PASSWORD=
    ```

4. **Install PHP Dependencies**

    ```sh
    composer install
    ```

5. **Install Frontend Dependencies**

    ```sh
    npm install
    ```

6. **Run Migrations and Seed Database**

    ```sh
    php artisan migrate --seed
    ```

7. **Start the Development Server**

    ```sh
    php artisan serve
    ```

8. **Access the Application**

    Open your browser and navigate to `http://localhost:8000`.

## Usage

### Muscle Selection Map

- Navigate to the muscle map section.
- Select a muscle group to view exercises targeting that group.

### Google Maps Integration

- Access the gym locator to find nearby gyms in Riga.
- Use the map interface to view detailed information about each gym.

### Exercise Library

- Browse through the categorized exercises.
- View detailed instructions, photos, and videos for each exercise.

### Training Programs

- Explore various training programs tailored to different fitness goals.
- Select a program to view its detailed plan and instructions.

### Personalized Workout and Nutrition Calculators

- Enter your personal details to generate a customized workout plan.
- Use the nutrition calculator to create a diet plan based on your goals.

## Contributing

We welcome contributions from the community! To contribute:

1. Fork the repository.
2. Create a new branch.
3. Make your changes.
4. Submit a pull request with a detailed description of your changes.

## License

This project is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

## Contact

For questions or support, please reach out to:

- Email: [jevgenijs.bubins@gmail.com](mailto:jevgenijs.bubins@gmail.com)
- Instagram: [evgeni_ibubin](https://www.instagram.com/evgeni_ibubin)

---

Feel free to reach out if you have any questions or need further assistance. Happy fitness journey with GymBridges!
