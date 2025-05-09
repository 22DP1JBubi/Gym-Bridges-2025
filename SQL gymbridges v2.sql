-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 09, 2025 at 03:29 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gymbridges`
--

-- --------------------------------------------------------

--
-- Table structure for table `exercises`
--

CREATE TABLE `exercises` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `muscle_group` varchar(100) DEFAULT NULL,
  `equipment` varchar(100) DEFAULT NULL,
  `difficulty` tinyint(4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `image` varchar(255) DEFAULT NULL,
  `instruction` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exercises`
--

INSERT INTO `exercises` (`id`, `name`, `description`, `video_url`, `category`, `muscle_group`, `equipment`, `difficulty`, `created_at`, `image`, `instruction`) VALUES
(13, 'Barbell Curl', 'Barbell Curl is a fundamental arm exercise focused on building the biceps using a barbell. It’s a compound isolation movement that primarily targets the biceps brachii and helps develop arm strength and size.', 'https://www.youtube.com/watch?v=kwG2ipFRgfo', 'arms', 'Biceps', 'Barbell', 2, '2025-05-08 03:35:51', 'uploads/Barbell Or EZ-Bar Curl.jpg', 'Stand upright holding a barbell with an underhand grip (palms facing forward), hands shoulder-width apart.\r\n\r\nKeep your elbows close to your torso and your back straight.\r\n\r\nExhale and curl the barbell upward by contracting your biceps. Only your forearms should move.\r\n\r\nContinue raising the barbell until your biceps are fully contracted and the bar is at shoulder level.\r\n\r\nHold for a brief pause while squeezing the biceps.\r\n\r\nInhale and slowly lower the barbell back to the starting position.'),
(14, 'Chin-Ups', 'Chin-ups are a bodyweight pulling exercise that primarily targets the biceps and upper back. By using a supinated (underhand) grip, more emphasis is placed on the biceps compared to traditional pull-ups.', 'https://www.youtube.com/watch?v=T78xCiw_R6g&ab_channel=LIVESTRONG.COM', 'arms,back', 'Biceps,Forearms,Lats', 'Pull-up bar', 3, '2025-05-08 03:53:10', 'uploads/chin-ups.jpg', 'Grip the pull-up bar with your palms facing you (underhand grip), hands shoulder-width apart.\r\n\r\nHang with arms fully extended and legs slightly bent if needed.\r\n\r\nPull your body upward until your chin clears the bar.\r\n\r\nPause briefly at the top.\r\n\r\nLower yourself in a controlled motion until arms are fully extended.\r\n\r\nRepeat.'),
(15, 'Dumbbell Curl', 'The dumbbell curl is an isolation exercise that targets the biceps. It involves curling a dumbbell in each hand while maintaining control throughout the movement. This helps build arm strength and muscle definition.', 'https://www.youtube.com/watch?v=ykJmrZ5v0Oo', 'arms,', 'Biceps,', 'Dumbbells,', 2, '2025-05-08 04:01:22', 'uploads/dumbbell-biceps-curl.jpg', 'Stand upright with a dumbbell in each hand, arms fully extended and palms facing forward.\r\n\r\nKeep your elbows close to your torso.\r\n\r\nCurl the weights while contracting your biceps.\r\n\r\nPause at the top of the movement, then slowly lower the dumbbells to the starting position.\r\n\r\nRepeat for the desired number of reps.'),
(16, 'Concentration Curl', 'The concentration curl is a focused isolation exercise that targets the biceps brachii. By eliminating momentum and using a seated position, it maximizes contraction and improves bicep peak development.', 'https://www.youtube.com/watch?v=Jvj2wV0vOYU&ab_channel=ScottHermanFitness', 'arms,', 'Biceps,', 'Dumbbells,', 2, '2025-05-08 05:17:43', 'uploads/concentration-curl.jpg', 'Sit on a bench with your legs spread and hold a dumbbell in one hand.\r\n\r\nLean forward slightly and rest your elbow against your inner thigh.\r\n\r\nCurl the dumbbell upward while keeping your upper arm stationary.\r\n\r\nSqueeze the bicep at the top of the movement.\r\n\r\nSlowly lower the weight back to the starting position and repeat.'),
(17, 'Cable Curl', 'The cable curl is a bicep isolation exercise that provides constant resistance throughout the movement. It’s an effective way to target the biceps with controlled tension and minimized cheating.', 'https://www.youtube.com/watch?v=rfRdD5PKrko&t=33s&ab_channel=TurnFit-VancouverPersonalTrainers', 'arms', 'Biceps', 'Cable machine', 2, '2025-05-08 05:24:17', 'uploads/cable-curl.jpg', 'Stand facing a low pulley cable machine with a straight bar attachment.\r\n\r\nGrasp the bar with an underhand grip, shoulder-width apart.\r\n\r\nKeep your elbows close to your sides and curl the bar toward your chest.\r\n\r\nSqueeze your biceps at the top.\r\n\r\nSlowly return to the starting position and repeat.'),
(18, 'Straight Bar Tricep Extension', 'The Straight Bar Tricep Extension is an isolation exercise that targets the triceps using a cable machine and a straight bar attachment. It allows for controlled resistance throughout the entire range of motion.\r\n\r\n', 'https://www.youtube.com/watch?v=LlBqt8dksdk&ab_channel=FunctionalAF', 'arms', 'Triceps', 'Straight bar,Cable machine', 2, '2025-05-08 06:31:17', 'uploads/Straight Bar Tricep Extension.png', 'Attach a straight bar to a high pulley on a cable machine.\r\n\r\nStand upright with feet shoulder-width apart, grasp the bar with an overhand grip (palms facing down), hands about shoulder-width apart.\r\n\r\nKeep elbows close to your body and stationary.\r\n\r\nPush the bar downward by extending your arms fully.\r\n\r\nPause at the bottom, squeezing the triceps.\r\n\r\nSlowly return to the starting position.\r\n\r\nRepeat for the desired number of reps.'),
(19, 'EZ Bar Skullcrusher', 'The EZ Bar Skullcrusher is an isolation triceps exercise that targets all three heads of the triceps. Performed lying on a bench, the lifter lowers an EZ bar from an extended position above the head to just above the forehead and then extends the arms back up. The unique shape of the EZ bar reduces wrist strain compared to a straight bar.', 'https://www.youtube.com/watch?v=d_KZxkY_0cM&ab_channel=ScottHermanFitness', 'arms', 'Triceps', 'EZ-bar,Bench', 3, '2025-05-08 06:37:29', 'uploads/Skullcrusher.jpg', 'Lie on a flat bench while holding an EZ curl bar with an overhand grip, hands shoulder-width apart.\r\n\r\nExtend your arms so the bar is held above your chest.\r\n\r\nSlowly lower the bar by bending your elbows until it\'s just above your forehead.\r\n\r\nPause, then push the bar back up to the starting position by extending your elbows.\r\n\r\nRepeat for the desired number of repetitions.'),
(20, 'Tricep Dip', 'Tricep Dips on parallel bars are a powerful bodyweight exercise that primarily targets the triceps while also engaging the chest and shoulders. The movement involves lowering and lifting the body between two bars using arm strength. By keeping the torso upright and the elbows tucked, the emphasis remains on the triceps rather than the chest.\r\n\r\n', 'https://www.youtube.com/watch?v=2z8JmcrW-As&ab_channel=Calisthenicmovement', 'arms,chest', 'Triceps,Shoulders,Chest Muscles', 'Dip bars', 3, '2025-05-08 07:16:30', 'uploads/Dip.jpg', 'Grasp parallel bars and lift your body with arms fully extended.\r\n\r\nKeep your torso upright and elbows close to your body.\r\n\r\nSlowly lower yourself by bending your elbows to a 90-degree angle.\r\n\r\nPause briefly at the bottom, feeling the stretch in your triceps.\r\n\r\nPush back up to the starting position without locking your elbows.\r\n\r\nRepeat for the desired number of repetitions.\r\n\r\nTip: Keep your body as vertical as possible to maximize triceps activation and reduce chest involvement.'),
(21, 'Rope Tricep Extension', 'Rope Tricep Extensions are an isolation cable exercise that focuses specifically on building and defining the triceps. Using a rope attachment allows for a greater range of motion at the bottom of the movement as you separate the rope ends, maximizing triceps contraction.', 'https://www.youtube.com/watch?v=vB5OHsJ3EME&ab_channel=ScottHermanFitness', 'arms', 'Triceps', 'Cable machine,Rope attachment', 2, '2025-05-08 07:21:28', 'uploads/rope-tricep-extension-1.jpg', 'Attach a rope handle to a high pulley.\r\n\r\nStand upright, grip the rope with both hands (palms facing inward), and keep your elbows close to your sides.\r\n\r\nStarting with your elbows bent at a 90-degree angle, extend your arms downward.\r\n\r\nAs you straighten your arms, pull the rope ends apart for maximum triceps activation.\r\n\r\nSlowly return to the starting position.\r\n\r\nRepeat.\r\n\r\nTip: Do not let your elbows flare out or swing your body — keep the motion strict.'),
(22, 'Dumbbell Tricep Kickback', 'The Dumbbell Tricep Kickback is an isolation exercise that targets the triceps. It involves extending the elbow joint while keeping the upper arm stationary. It’s a popular finishing movement that helps in defining and toning the triceps, especially the long head.', 'https://www.youtube.com/watch?v=6SS6K3lAwZ8&ab_channel=ScottHermanFitness', 'arms,', 'Triceps,', 'Dumbbells,', 2, '2025-05-08 07:54:36', 'uploads/triceps-kickback.jpg', 'Hold a dumbbell in each hand with a neutral grip.\r\n\r\nBend your knees slightly and hinge forward at the hips, keeping your back straight.\r\n\r\nTuck your elbows close to your torso; your upper arms should be parallel to the floor.\r\n\r\nExtend your elbows to push the dumbbells straight back.\r\n\r\nSqueeze your triceps at the top.\r\n\r\nSlowly return to the starting position.\r\n\r\nRepeat.\r\n\r\nTip: Keep your upper arms stationary throughout — only your forearms should move'),
(23, 'Military Press (Overhead Press)', 'The Military Press is a compound upper-body exercise that primarily targets the shoulders and engages the triceps and upper chest. It involves pressing a barbell or dumbbells from shoulder height to overhead in a standing position with strict posture — feet together, no leg drive — which differentiates it from the standard Overhead Press.', 'https://www.youtube.com/watch?v=2yjwXTZQDDI&ab_channel=ScottHermanFitness', 'arms,back,chest,', ',Shoulders,Triceps,Chest Muscles,Trapezius', 'Barbell,', 3, '2025-05-08 08:03:29', 'uploads/Military-Press.jpg', 'Stand upright with your feet close together (military stance).\r\n\r\nHold the barbell at shoulder level with an overhand grip, elbows slightly forward.\r\n\r\nBrace your core and glutes.\r\n\r\nPress the bar straight overhead until your arms are fully extended.\r\n\r\nLower the bar back to shoulder level with control.\r\n\r\nRepeat for the desired number of repetitions.\r\n\r\nTip: Keep your torso rigid — do not lean back or use leg momentum. This strict form isolates the deltoids more effectively.'),
(24, 'Dumbbell Lateral Raise', 'Dumbbell Lateral Raise is an isolation exercise targeting the lateral deltoids. It\'s commonly used to build width in the shoulders and improve upper body aesthetics.\r\n', 'https://www.youtube.com/watch?v=3VcKaXpzqRo&ab_channel=ScottHermanFitness', 'arms', 'Shoulders', 'Dumbbells', 2, '2025-05-08 09:10:01', 'uploads/Dumbbell Lateral Raise.jpg', 'Stand with your feet shoulder-width apart, holding a dumbbell in each hand at your sides, palms facing inward.\r\n\r\nKeep a slight bend in your elbows and your back straight.\r\n\r\nRaise the dumbbells out to the sides until your arms are parallel to the ground.\r\n\r\nPause briefly at the top, then slowly lower the dumbbells back to the starting position.\r\n\r\nAvoid swinging or using momentum. Focus on using your shoulder muscles to lift the weights.\r\n\r\n'),
(25, 'Standing Dumbbell Front Raise', 'The Standing Dumbbell Front Raise targets the anterior (front) deltoids and helps build shoulder strength and definition. It\'s a great isolation movement for improving the appearance of the shoulders.', 'https://www.youtube.com/watch?v=-t7fuZ0KhDA&ab_channel=ScottHermanFitness', 'arms,', 'Shoulders,', 'Barbell,', 2, '2025-05-08 09:13:40', 'uploads/Standing Dumbbell Front Raise.jpg', 'Stand upright with your feet shoulder-width apart, holding a dumbbell in each hand in front of your thighs with your palms facing your body.\r\n\r\nKeep your arms straight but slightly bent at the elbows.\r\n\r\nRaise one or both dumbbells straight in front of you to shoulder height.\r\n\r\nPause briefly, then lower them slowly back to the starting position.\r\n\r\nDo not use momentum or swing the weights.'),
(26, 'Cable Face Pull', 'The Cable Face Pull is an excellent exercise for strengthening the rear deltoids, trapezius, and rotator cuff muscles. It improves posture, shoulder health, and stability, especially important for people doing heavy pressing movements.\r\n\r\n', 'https://www.youtube.com/watch?v=rep-qVOkqgk&ab_channel=ScottHermanFitness', 'arms,back', 'Shoulders,Trapezius', 'Smith machine,Rope attachment', 3, '2025-05-08 09:19:13', 'uploads/face-pull-cable-exercise-cover.jpg', 'Attach a rope handle to a high pulley on a cable machine.\r\n\r\nGrab the rope with both hands using a neutral grip (palms facing in), step back slightly, and lean back just a bit.\r\n\r\nPull the rope towards your face, keeping your upper arms parallel to the ground and flaring your elbows out.\r\n\r\nSqueeze your shoulder blades together at the peak of the movement.\r\n\r\nReturn to the starting position with control.'),
(32, 'Seated Arnold Press', 'The Seated Arnold Press is a shoulder-strengthening exercise developed by Arnold Schwarzenegger. It targets all three heads of the deltoid muscle by combining a rotation with a standard overhead press. This movement promotes greater shoulder engagement and stability.', 'https://www.youtube.com/watch?v=vj2w851ZHRM&ab_channel=Instructionalfitness', 'arms', 'Shoulders', 'Bench,Dumbbells', 3, '2025-05-09 08:49:56', 'uploads/2_0f397b69-fbe0-4555-bade-e5b5c2723fc0.jpg', 'Sit on a bench with back support, holding two dumbbells in front of you at shoulder height, palms facing your body.\r\n\r\nAs you press the weights upward, rotate your palms outward.\r\n\r\nAt the top of the movement, your palms should face forward and arms fully extended.\r\n\r\nSlowly reverse the motion and bring the dumbbells back to the starting position.\r\n\r\nRepeat for the desired number of reps.'),
(33, 'Seated Barbell Wrist Curl', 'The Seated Barbell Wrist Curl is an isolation exercise targeting the forearm flexors, performed while seated with the forearms resting on the thighs or a bench. It’s ideal for building grip strength and forearm mass.', 'https://www.youtube.com/watch?v=hSukP162H4M&ab_channel=JoshuaDavidTaubes', 'arms', 'Forearms', 'Barbell', 2, '2025-05-09 08:58:21', 'uploads/Seated Barbell Wrist Curl.jpg', 'Sit on a flat bench and hold a barbell with an underhand grip, resting your forearms on your thighs.\r\n\r\nAllow your wrists to extend fully so the barbell rolls down toward your fingers.\r\n\r\nCurl the barbell up using only your wrists, squeezing at the top.\r\n\r\nSlowly lower the barbell back to the starting position.\r\n\r\nRepeat for the desired number of repetitions.'),
(34, 'Behind-The-Back Barbell Wrist Curl', 'The Behind-the-Back Barbell Wrist Curl is an isolation exercise that targets the forearm flexors. By holding the barbell behind the back and allowing the wrists to flex downward, this movement effectively strengthens grip and forearm size.', 'https://www.youtube.com/watch?v=xrS1UCC24do&ab_channel=TestosteroneNation', 'arms', 'Forearms', 'Barbell', 2, '2025-05-09 09:01:02', 'uploads/Behind-The-Back Barbell Wrist Curl.jpg', 'Stand upright and grasp a barbell behind your back using a shoulder-width underhand grip (palms facing forward).\r\n\r\nAllow the bar to rest against your glutes with arms fully extended.\r\n\r\nLet the barbell roll down slightly in your hands so your wrists extend fully downward.\r\n\r\nCurl the barbell upward by flexing your wrists, keeping your arms stationary.\r\n\r\nSqueeze at the top of the movement, then slowly lower the barbell back to the starting position.\r\n\r\nRepeat for the desired number of repetitions.'),
(35, 'One-Arm Seated Dumbbell Wrist Curl', 'The One-Arm Seated Dumbbell Wrist Curl isolates the forearm flexors using a single dumbbell, allowing focused development and correction of strength imbalances between arms.', 'https://www.youtube.com/watch?v=Q7dTbE4kRUY&ab_channel=Musqle', 'arms', 'Forearms', 'Dumbbells', 2, '2025-05-09 09:04:38', 'uploads/One-Arm Seated Dumbbell Wrist Curl.jpg', 'Sit on a bench and rest your forearm on your thigh or a flat surface, palm facing upward.\r\n\r\nHold a dumbbell in your hand, letting your wrist extend so the dumbbell lowers slightly.\r\n\r\nCurl the dumbbell upward by flexing your wrist, keeping the rest of your arm stationary.\r\n\r\nSqueeze at the top, then slowly lower the dumbbell back to the starting position.\r\n\r\nPerform all reps on one arm, then switch to the other.'),
(36, 'Reverse Grip Dumbbell Wrist Curl (Over Bench)', 'This exercise targets the forearm extensors, helping improve wrist stability and overall forearm development. It is performed using a reverse grip with the palms facing downward.', 'https://www.youtube.com/watch?v=ry4oASDKD-o&ab_channel=KasKuvvet', 'arms', 'Forearms', 'Dumbbells', 2, '2025-05-09 09:47:48', 'uploads/reverse_grip_dumbbell_wrist_curl_over_bench.jpg', 'Sit on a bench and place your forearms on your thighs or a flat surface, with your palms facing downward over the edge.\r\n\r\nHold a dumbbell in each hand, allowing your wrists to drop slightly below the bench level.\r\n\r\nRaise the dumbbells by extending your wrists upward, isolating the forearm extensors.\r\n\r\nHold for a moment at the top, then slowly lower the weights back down.\r\n\r\nKeep the movement controlled and avoid using momentum.'),
(37, 'Reverse One Arm Cable Curl', 'This isolation exercise targets the forearm extensors and brachioradialis. Using a reverse grip with one arm allows greater focus and balance during execution.', 'youtube.com/watch?v=SIuDkLLlL6E&ab_channel=ReturnoftheAthlete-Testimonials%26Podcast', 'arms', 'Forearms', 'Cable machine,Single handle', 2, '2025-05-09 09:57:01', 'uploads/reverseonearmcablecurl1.jpg', 'Attach a single handle to a low pulley on a cable machine. Stand side-on to the machine and grasp the handle with an overhand grip (palm facing down). Keep your elbow close to your body and curl the handle towards your shoulder, focusing on keeping the wrist straight and the motion controlled. Pause at the top, then slowly return to the start position. Repeat and switch arms.'),
(38, 'Barbell Bench Press', 'The barbell bench press is a classic compound exercise that primarily targets the chest, triceps, and front deltoids. It is one of the most effective movements for building upper body strength and mass.', 'https://www.youtube.com/watch?v=gRVjAtPip0Y&ab_channel=BuffDudes', 'chest,arms', 'Chest Muscles,Triceps,Shoulders', 'Bench,Barbell', 3, '2025-05-09 10:02:55', 'uploads/Barbell Bench Press.jpg', 'Lie flat on a bench with your eyes directly under the barbell.\r\n\r\nGrip the bar slightly wider than shoulder-width apart.\r\n\r\nUnrack the bar and slowly lower it to your mid-chest, keeping your elbows at a 45° angle.\r\n\r\nPress the bar back up to the starting position, fully extending your arms without locking the elbows.\r\n\r\nRepeat for the desired number of repetitions.'),
(39, 'Incline Dumbbell Bench Press', 'The incline dumbbell bench press is a strength-building upper body exercise that primarily targets the upper chest (clavicular head of the pectorals) while also working the shoulders and triceps. The incline angle increases emphasis on the upper chest.\r\n', 'https://www.youtube.com/watch?v=8iPEnn-ltC8&ab_channel=ScottHermanFitness', 'chest,arms', 'Chest Muscles,Triceps,Shoulders', 'Dumbbells,Bench', 3, '2025-05-09 10:07:25', 'uploads/1_8c4ca767-1b7d-4981-9c20-c7d0b744dca5.jpg', 'Set an adjustable bench to a 30–45 degree incline and sit with a dumbbell in each hand resting on your thighs.\r\n\r\nLie back and position the dumbbells at shoulder level, palms facing forward.\r\n\r\nPress the dumbbells up until your arms are fully extended above your chest.\r\n\r\nSlowly lower the dumbbells back to the starting position, keeping control.\r\n\r\nRepeat for the desired number of reps.\r\n\r\n'),
(40, 'Dumbbell Flys', 'The dumbbell fly is an isolation chest exercise that emphasizes the pectoral muscles by stretching and contracting them through a wide arc of motion. It helps develop chest width and definition.\r\n\r\n', 'https://www.youtube.com/watch?v=eozdVDA78K0&ab_channel=ScottHermanFitness', 'chest,arms', 'Chest Muscles,Shoulders', 'Bench,Dumbbells', 3, '2025-05-09 10:13:44', 'uploads/Dumbbell_Chest_Fly_825bd98f-7e67-4b98-ba86-3db39e835290_600x600_crop_center.jpg', 'Lie flat on a bench holding a dumbbell in each hand, arms extended above your chest with palms facing each other.\r\n\r\nWith a slight bend in your elbows, slowly lower the dumbbells out to your sides in a wide arc until you feel a stretch in your chest.\r\n\r\nBring the dumbbells back up by squeezing your chest muscles, keeping the same arc in reverse.\r\n\r\nKeep your back pressed to the bench and avoid bending your elbows more during the movement.\r\n\r\nRepeat for the desired number of reps.\r\n\r\n'),
(41, 'Pec Deck', 'The Pec Deck is an isolation chest exercise that targets the pectoral muscles by bringing the arms together in a hugging motion using a machine.', 'https://www.youtube.com/watch?v=eGjt4lk6g34&ab_channel=PureGym', 'chest', 'Chest Muscles', 'Pec deck machine', 2, '2025-05-09 11:11:53', 'uploads/ChestFly.jpg', 'Sit on the pec deck machine and adjust the seat so the handles are chest-level.\r\n\r\nGrasp the handles with your arms slightly bent.\r\n\r\nSlowly bring your arms together in front of you in a wide arc, squeezing your chest.\r\n\r\nPause at the peak contraction.\r\n\r\nSlowly return to the starting position.\r\n\r\nRepeat for the desired number of reps.'),
(42, 'Standing Cable Fly', 'Standing Cable Fly is an isolation exercise that targets the pectoral muscles using a cable machine. It is performed in a standing position, emphasizing the contraction of the chest throughout the movement.', 'Standing Cable Fly', 'chest', 'Chest Muscles', 'Cable machine,Single handle', 2, '2025-05-09 11:20:20', 'uploads/cable-fly-800.jpg', 'Stand between two high pulleys with a handle in each hand.\r\n\r\nStep slightly forward with one foot, keeping your back straight and core engaged.\r\n\r\nWith a slight bend in your elbows, bring your hands together in front of your chest in a wide arc.\r\n\r\nSqueeze your chest muscles at the peak, then slowly return to the starting position.\r\n\r\nRepeat for the desired number of reps.'),
(43, 'Cable Crunch', 'Cable Crunch is a weighted abdominal exercise that effectively targets the upper abdominal muscles by using a cable machine in a kneeling position. It emphasizes spinal flexion under resistance.', 'https://www.youtube.com/watch?v=6GMKPQVERzw&ab_channel=RenaissancePeriodization', 'abs', 'Abdominal Muscles', 'Cable machine,Rope attachment', 2, '2025-05-09 11:29:32', 'uploads/1_f607c1e6-fb1a-45f8-bb2e-4dbf0ea4827f.jpg', 'Attach a rope handle to a high pulley on a cable machine.\r\n\r\nKneel down facing the machine and grab the rope with both hands, placing your hands beside your head.\r\n\r\nKeep your hips stationary and bend at the waist, crunching your abs to bring your elbows toward your knees.\r\n\r\nSqueeze at the bottom, then slowly return to the starting position.\r\n\r\nRepeat for the desired number of repetitions.'),
(44, 'Lying Floor Leg Raise', 'The Lying Floor Leg Raise is a bodyweight exercise that primarily targets the lower abdominal muscles. It is ideal for building core strength and improving control over hip flexion.', 'https://www.youtube.com/watch?v=9qbV7ZQNqqA&ab_channel=HealthHunt', 'abs', 'Abdominal Muscles', 'Mat', 2, '2025-05-09 11:33:20', 'uploads/lying-leg-raise-800.jpg', 'Lie flat on your back on the floor or a mat, legs fully extended, arms down at your sides for support.\r\n\r\nWhile keeping your legs straight, slowly lift them toward the ceiling until they form a 90-degree angle with your torso.\r\n\r\nPause briefly at the top, then slowly lower your legs back down without letting your feet touch the ground.\r\n\r\nMaintain control and avoid arching your lower back.\r\n\r\nRepeat for the desired number of reps.'),
(45, 'Decline Bench Sit Up', 'The Decline Bench Sit Up is a classic abdominal exercise performed on a decline bench. It increases resistance on the core muscles and helps develop definition and strength in the upper and lower abdominals.', 'https://www.youtube.com/watch?v=N7hf1_vcX5w&ab_channel=PureGym', 'abs', 'Abdominal Muscles', 'Decline bench', 3, '2025-05-09 11:38:17', 'uploads/decline-sit-up-800.jpg', 'Sit on a decline bench and secure your feet under the padded rollers.\r\n\r\nCross your arms over your chest or place your hands behind your head.\r\n\r\nSlowly lower your upper body backward until your back is just above the bench.\r\n\r\nEngage your abs and sit back up to the starting position without using momentum.\r\n\r\nExhale as you come up and inhale as you lower yourself down.\r\n\r\nRepeat for the desired number of repetitions.'),
(46, 'Plank', 'The plank is an isometric core strength exercise that involves maintaining a position similar to a push-up for the maximum possible time. It strengthens the abdominal muscles, lower back, and shoulders.', 'https://www.youtube.com/watch?v=pSHjTRCQxIw&ab_channel=ScottHermanFitness', 'abs', 'Abdominal Muscles', '', 2, '2025-05-09 11:41:35', 'uploads/c_C3_B3mo-hacer-una-plancha-abdominal.jpg', 'Lie face down on the floor, then lift your body onto your forearms and toes.\r\n\r\nKeep your elbows directly under your shoulders, forearms flat on the floor.\r\n\r\nKeep your body in a straight line from head to heels.\r\n\r\nEngage your core, glutes, and legs.\r\n\r\nHold the position as long as you can while maintaining form.'),
(47, 'Roman Chair Leg Raise', 'Roman Chair Leg Raise is an effective abdominal exercise performed on a Roman chair or captain’s chair. It targets the lower abdominals and hip flexors, helping improve core strength and stability.', 'https://www.youtube.com/watch?v=9FeC5SAB_3g&ab_channel=FitGent', 'abs', 'Abdominal Muscles', 'Roman chair', 3, '2025-05-09 11:45:35', 'uploads/Roman Chair Leg Raise.jpg', 'Stand on the Roman chair with your forearms resting on the pads and back supported.\r\n\r\nLet your legs hang straight down.\r\n\r\nEngage your core and slowly raise your legs in front of you until they\'re parallel to the floor.\r\n\r\nPause at the top, then lower your legs with control.\r\n\r\nRepeat for desired reps.'),
(48, 'Lat Pulldown', 'The Lat Pulldown is a classic compound back exercise that targets the latissimus dorsi muscles. It\'s performed on a cable machine using a wide bar and is ideal for developing upper back width and improving posture.', 'https://www.youtube.com/watch?v=CAwf7n6Luuc&ab_channel=ScottHermanFitness', 'back', 'Lats,Back Muscles,Teres major', 'Cable machine,Straight bar', 3, '2025-05-09 12:33:38', 'uploads/Does-Lat-Pulldown-Work-Forearms.jpg', 'Sit at the lat pulldown machine and adjust the thigh pad to keep your legs locked in place.\r\n\r\nGrab the bar with a wide overhand grip.\r\n\r\nPull the bar down toward your upper chest, squeezing your shoulder blades together.\r\n\r\nPause briefly, then slowly let the bar return to the starting position with control.\r\n\r\nRepeat for desired reps.'),
(49, 'Wide Grip Pull-Up', 'The Wide Grip Pull-Up is a challenging bodyweight exercise that emphasizes the upper lats and overall back development. The wider grip increases the difficulty by minimizing arm involvement and isolating the back muscles more effectively.', 'https://www.youtube.com/watch?v=eGo4IYlbE5g&ab_channel=Calisthenicmovement', 'back', 'Lats,Back Muscles,Teres major', 'Pull-up bar', 4, '2025-05-09 13:08:13', 'uploads/Wide Grip Pull-Up.jpg', 'Grab a pull-up bar with a wide overhand grip — hands wider than shoulder-width apart.\r\n\r\nHang with your arms fully extended and chest up.\r\n\r\nPull yourself up until your chin passes above the bar, focusing on engaging your back.\r\n\r\nSlowly lower yourself back to the starting position with control.\r\n\r\nRepeat for desired repetitions.'),
(50, 'Straight Arm Lat Pull Down', 'The Straight Arm Lat Pulldown is an isolation exercise targeting the latissimus dorsi. It emphasizes a full stretch and contraction of the lats without significant involvement of the biceps, making it ideal for improving mind-muscle connection and back width.', 'https://www.youtube.com/watch?v=G9uNaXGTJ4w&ab_channel=RenaissancePeriodization', 'back', 'Lats,Back Muscles,Teres major', 'Cable machine,Straight bar', 3, '2025-05-09 13:13:02', 'uploads/18e9762c1af6a35e4a8c4683dd6e7942.jpg', 'Stand in front of a cable machine with a straight bar attached to the high pulley.\r\n\r\nGrab the bar with a shoulder-width overhand grip and take a step back, keeping your arms straight.\r\n\r\nHinge slightly at the hips and engage your core.\r\n\r\nPull the bar down in an arc motion until your hands reach your thighs.\r\n\r\nPause and squeeze your lats, then return slowly to the starting position.\r\n\r\nRepeat for the desired reps.'),
(51, 'V-Bar Pull Down', 'The V-Bar Pulldown is a cable machine exercise that targets the latissimus dorsi and middle back muscles. Using a V-bar attachment promotes a neutral grip, which can reduce stress on the wrists and shoulders while encouraging a strong contraction in the lats.', 'https://www.youtube.com/watch?v=LJ5ebC1pWkA&ab_channel=BocaRatonPersonalTraining', 'back', 'Lats,Teres major,Back Muscles', 'Cable machine,V-bar attachment', 3, '2025-05-09 13:17:21', 'uploads/D47322CA-0A5F-4C9B-B558-BFC2CF2DB610.jpeg', 'Sit down at the lat pulldown machine and attach a V-bar handle to the top pulley.\r\n\r\nGrab the V-bar with a neutral grip (palms facing each other).\r\n\r\nPull the bar down toward your upper chest while leaning back slightly.\r\n\r\nSqueeze your back muscles at the bottom of the movement.\r\n\r\nSlowly return to the starting position under control.\r\n\r\nRepeat for the desired number of repetitions.'),
(52, ' Iso-Lateral Lat Pulldown', 'The Iso-Lateral Lat Pulldown is a strength exercise performed on a machine with two independent handles, allowing each side of the back to work independently. It targets the latissimus dorsi while also engaging the biceps and other upper back muscles. This movement promotes muscular balance and isolation, especially useful for correcting asymmetries.', 'https://www.youtube.com/watch?v=J8rBALm7Ij0&ab_channel=ClubConnect', 'back', 'Lats,Trapezius,Teres major', 'Hammer Strength Machine', 3, '2025-05-09 13:25:52', 'uploads/Plate_Loaded_Chest_and_Shoulder_Machine-1.jpg', 'Adjust the seat and thigh pad so your legs are firmly secured.\r\n\r\nSit upright and grasp each independent handle above you with a neutral or overhand grip.\r\n\r\nBegin with your arms fully extended upward.\r\n\r\nPull both handles down simultaneously or one at a time toward your upper chest by engaging your lats.\r\n\r\nSqueeze your shoulder blades together at the bottom.\r\n\r\nSlowly return to the starting position with control.\r\n\r\nRepeat for the desired number of repetitions.\r\n\r\n');

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE `notes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `note` text NOT NULL,
  `is_important` tinyint(1) DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`id`, `user_id`, `note`, `is_important`, `image`, `created_at`, `updated_at`) VALUES
(1, 1, 'fwefwefwe342234234', 1, 'uploads/IMG_6245.jpg', '2025-03-26 15:22:53', '2025-03-26 15:23:23'),
(2, 2, 'Šodiens man bija ternins', 1, 'uploads/IMG_6245.jpg', '2025-03-26 16:14:45', '2025-03-26 16:14:54');

-- --------------------------------------------------------

--
-- Table structure for table `progress`
--

CREATE TABLE `progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `weight` decimal(5,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `progress`
--

INSERT INTO `progress` (`id`, `user_id`, `date`, `weight`) VALUES
(1, 1, '2025-03-26', 70.00),
(2, 1, '2025-03-28', 75.00),
(3, 1, '2025-03-30', 80.00),
(4, 1, '2025-04-14', 90.00),
(5, 1, '2025-04-24', 50.00),
(6, 2, '2025-03-26', 50.00),
(8, 2, '2025-04-02', 60.00),
(10, 2, '2025-04-10', 80.00);

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `task` text NOT NULL,
  `is_completed` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `due_date` date DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `user_id`, `task`, `is_completed`, `created_at`, `due_date`, `completed_at`) VALUES
(1, 1, 'gdfgdfgd123', 0, '2025-03-26 15:22:13', NULL, NULL),
(3, 1, '1234567', 0, '2025-03-26 15:22:30', NULL, NULL),
(4, 2, 'Bench press 100kg', 0, '2025-03-26 16:13:50', NULL, NULL),
(6, 8, '123', 0, '2025-04-20 08:57:39', NULL, NULL),
(7, 8, 'gdfgdfgd', 0, '2025-04-20 08:57:48', '2025-04-23', NULL),
(8, 8, 'Bench press 100kg', 0, '2025-04-20 11:09:17', NULL, NULL),
(9, 8, 'пвапвап', 0, '2025-04-20 11:09:36', '2025-06-19', NULL),
(10, 8, 'asdfgfgrteg', 0, '2025-04-20 13:16:09', NULL, NULL),
(11, 8, 'zxcsdfsd', 0, '2025-04-20 13:16:12', NULL, NULL),
(12, 1, 'apple', 0, '2025-04-21 06:05:52', '2025-04-25', NULL),
(13, 1, 'Banana', 0, '2025-04-21 06:05:52', '2025-04-20', NULL),
(14, 1, 'zebra', 0, '2025-04-21 06:05:52', '2025-04-19', NULL),
(15, 1, 'ALMOND', 0, '2025-04-21 06:05:52', '2025-04-30', NULL),
(16, 1, 'bench press 100kg', 0, '2025-04-21 06:05:52', '2025-04-23', NULL),
(17, 1, 'Deadlift PR', 1, '2025-04-21 06:05:52', NULL, '2025-04-21 08:09:07'),
(18, 1, 'Xylophone warmup', 0, '2025-04-21 06:05:52', '2025-05-01', NULL),
(19, 1, 'gdfgdfgd', 0, '2025-04-21 06:05:52', '2025-04-27', NULL),
(20, 1, 'Run134', 0, '2025-04-21 06:05:52', NULL, NULL),
(21, 1, 'Push ups', 1, '2025-04-21 06:05:52', '2025-04-21', '2025-04-21 08:09:09'),
(22, 1, 'A very long and descriptive task title to test the sorter', 0, '2025-04-21 06:05:52', NULL, NULL),
(24, 1, 'Code review', 0, '2025-04-21 06:05:52', NULL, NULL),
(25, 1, 'milk', 0, '2025-04-21 06:05:52', '2025-04-22', NULL),
(27, 1, 'asdasdasd', 0, '2025-04-21 06:05:52', '2025-04-29', NULL),
(28, 1, 'Hello world234', 0, '2025-04-28 15:28:27', '2025-04-30', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userID` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `weight` decimal(5,2) NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `height` decimal(5,2) NOT NULL,
  `registrationDate` date NOT NULL,
  `birthdate` date DEFAULT NULL,
  `lastLoginDate` date DEFAULT NULL,
  `avatar` varchar(255) DEFAULT 'uploads/avatars/default_avatar.png',
  `isPremium` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `username`, `email`, `password`, `weight`, `gender`, `height`, `registrationDate`, `birthdate`, `lastLoginDate`, `avatar`, `isPremium`) VALUES
(1, 'Jevgenij', 'zhenjab21@gmail.com', '$2y$10$EOkJUaxAeIDIV6XPaN7egul4c17Pyt7Ip1Q3r/6y1RyxZ3dFUs4J2', 70.00, 'Male', 184.00, '2025-03-26', NULL, '2025-05-07', 'uploads/avatars/avatar_680f9e357af763.84221855.png', 0),
(2, 'Jevgenij123', 'zhenjab212@gmail.com', '$2y$10$pD/4aKaecCHXBmINz68ESeoyt8sjjyefypgl0TJKTKIpPRQttLhMm', 80.00, 'Male', 170.00, '2025-03-26', NULL, '2025-04-10', 'uploads/avatars/Screenshot 2025-03-26 191705.png', 0),
(3, 'Vasja', 'vacok123@gmail.com', '$2y$10$44fhaFSQ8Sa/O2MYURIt7Oif75LWb7Norv9.RfNJVW.psvTP5sAvi', 99.00, 'Male', 170.00, '2025-04-10', NULL, '2025-04-14', 'uploads/avatars/default_avatar.png', 0),
(4, 'Admin', 'admin@gmail.com', '$2y$10$sYuICU9jPuk3pO6OS1LFdugVcngskzQ23AUEsUCvLJP8lC.aPqn4y', 6.00, 'Male', 160.00, '2025-04-10', NULL, '2025-04-10', 'images/default_avatar.png', 0),
(5, 'Dencik', 'denten@gmail.com', '$2y$10$SxNa6UK.uaUri5wh1OAWpODlfkgQrMsfEX.wz/t0wJ.kl0kY.ZQyu', 50.00, 'Other', 176.00, '2025-04-14', '2007-06-14', NULL, 'images/default_avatar.png', 0),
(6, 'Vanja', 'vanja@mail.ru', '$2y$10$6XlgTZvIUkrIWtMpt4fiq.wV8hLTgrhoZ8sHyl2LcY0mthILMpRXq', 77.00, 'Male', 188.00, '2025-04-14', '1995-11-23', NULL, 'images/default_avatar.png', 0),
(7, 'Zhenjokk', 'zhenjab2ytryrt1@gmail.com', '$2y$10$jhqEtdjzDje1X2jGJe8wf.UiN2N.B50QoFAtFCv6gNqWRytUoob56', 70.00, 'Male', 185.00, '2025-04-14', '2005-06-15', '2025-04-21', 'images/default_avatar.png', 0),
(8, 'Tomas', 'info@palami.com', '$2y$10$/cmyYxDEVUzoEBo0x5hoheDvaSQYr4uzVJw09hYCCxP0asPLKv7FO', 70.00, 'Male', 190.00, '2025-04-14', '2024-04-14', '2025-04-28', 'uploads/avatars/avatar_67fcd6cb452d10.82494353.jpg', 0),
(9, 'Test_woman', 'woman@gmail.com', '$2y$10$tYrHZAnVnJWow1HPH7mB8eq4EAsKdIYWP56B.Qq47WI70lj80p7yq', 50.00, 'Female', 160.00, '2025-04-14', '2006-07-15', '2025-04-14', 'images/default_female_avatar.png', 0),
(10, 'test', 'test@gmail.com', '$2y$10$YcnhhjPcwc73LHhPPoNwyuOD9BnyEhRhBRhXhvZQ3edVYYPd8xU.y', 77.00, 'Other', 200.00, '2025-04-14', '1999-03-04', '2025-04-14', 'images/default_other_avatar.png', 0),
(11, 'rikgrayms', 'rikgrayms@gmail.com', '$2y$10$nTMLMxERMZbJbMo3z35Zs.urnuXp/bwk1I9C1uXVN8OlUeSArjJnS', 80.00, 'Male', 170.00, '2025-04-21', '1985-02-21', '2025-04-24', 'images/default_male_avatar.png', 0),
(12, 'mishon', 'mishon@gmail.cpm', '$2y$10$.wns5QS1MLgtBosb7sbXqOTjKksqkD8qSWBNqGdNfaZwj1mwaIBp2', 50.00, 'Female', 160.00, '2025-04-21', '1988-01-08', '2025-04-21', 'images/default_female_avatar.png', 0),
(13, 'zombie', 'zomb@mail.lv', '$2y$10$Z6gWqGpix2BXUAt.SV4fBeh0g2Q9Oxni2pZBD3h6ASBOpPO1zyaVy', 70.00, 'Other', 165.00, '2025-04-21', '2024-04-16', '2025-04-21', 'images/default_other_avatar.png', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `exercises`
--
ALTER TABLE `exercises`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `progress`
--
ALTER TABLE `progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `exercises`
--
ALTER TABLE `exercises`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `progress`
--
ALTER TABLE `progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`userID`) ON DELETE CASCADE;

--
-- Constraints for table `progress`
--
ALTER TABLE `progress`
  ADD CONSTRAINT `progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`userID`) ON DELETE CASCADE;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`userID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
