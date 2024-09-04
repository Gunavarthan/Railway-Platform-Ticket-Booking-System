# 🚆 Railway Platform Ticket Booking System

## 🧐 Overview

The Railway Platform Ticket Booking System is a web-based application that facilitates the booking and management of railway platform tickets. The application has two primary modes: User Mode and Admin Mode, each offering different functionalities tailored to specific users.

## 🤖 Technologies Used
- **HTML5**
- **CSS3**
- **🛢MySQL**
- **Ajax**
- **🐘 PHP (QRcode)**
- **｡🇯‌🇸‌ JavaScript (Chart JS)**

## ⚙️ Features

### Home Page
- **User Mode**: Provides access to the user interface for booking platform tickets.
- **Admin Mode**: Allows admins to log in and manage the platform ticketing system.

### 🛡️ Admin Mode

#### Admin Login
- **Admin Authentication**: Requires admin name and password for access.
- **Multiple Admin Support**: Allows multiple admins to manage the system.
- **Admin Management**: New admins can be added if necessary.

#### Search
- **Guest Information Search**: Search through guest information based on:
  - Platform number
  - Payment status
  - Junction
- **Combined Search**: Multiple search criteria can be combined for more specific queries.
- **Ticket ID Hover**: Hover over a Ticket ID to view all guest names along with their Aadhar numbers.

#### Advanced Search
- **Detailed Search Options**: Search through guest information based on:
  - Date range
  - Ticket ID
  - Junction
  - PNR
- **Sorting Operations**: Sort search results by date, ticket ID, or junction.
- **Export**: Ability to export search results to an Excel format.

#### Statistics
- **Guest Statistics**: Displays statistics such as:
  - Total Guests
  - Total Adults
  - Total Children
- **Revenue Insights**: Identifies the platform with the highest revenue and the month with the most collections.
- **Simple Search**: Basic search functionality for quick insights.
- **Graphical Reports**: Option to generate graphs for specified junctions, showing:
  - Ticket count
  - Total revenue
  - Total adults
  - Total children
  - Average collection

#### Transaction Management
- **Transaction Display**: Lists all transactions within a specified date range.
- **Transaction Details**: Shows bank details, account details, and payment status (failed/success).
- **Summary Display**: Provides a summary of:
  - Total transactions
  - Total amount collected within the specified date range.

## 🙍🏻‍♂️ User Mode
#### PNR Validation
- **User Prompt**: Users will be prompted to enter the PNR of the passenger they are accompanying.
- **PNR Existence**: Validation to check if the PNR exists in the system.
- **Travel Date Verification**: Ensures the passenger has a train journey scheduled for today.
- **Additional Checks**: Further validations may be performed to ensure the PNR is valid for booking.

#### Guest Details Entry
- **Input Fields**: Users must enter details such as:
  - Name
  - Aadhar Number
  - Phone Number
  - Number of Children
  - Junction
  - Platform Number
  - Secondary Contact Number
- **Aadhar Verification**: Aadhar is verified for safety measures.
- **Guest Limit**: A maximum of 5 guests, including children, can be added per ticket (Add & Remove functionality).

#### Payment and Ticket Generation
- **Payment Options**: After entering guest details, users are provided with payment options, and the total price is displayed.
- **Cancel Option**: Users can cancel the payment if needed.
- **Payment Authentication**: Users must enter a valid ID and Password to proceed with the payment.
- **Ticket Generation**: Upon successful payment, the ticket is generated with the following details:
  - Ticket ID
  - Ticket Date
  - Time
  - Validity End Time
  - Junction
  - Platform Number
  - Passenger’s PNR
  - Number of Guests
  - Number of Children
  - Number of Adults
  - Price
  - QR-Code (for a digital copy when scanned)

#### Additional Features
- **Hand Strap Option**: Users can opt for a hand strap, which is useful if the passenger is disabled or for children.
- **Print and Logout**: After generating the ticket/hand straps, users can print them and log out.

## 🎥 Video Demo
***In Progress***

## ╰┈➤ Page Flow Diagram 
![flowchart](https://github.com/user-attachments/assets/0fdc431b-7870-4fdb-a4d2-a8d9653f75fc)

## 🛢 Recreate the DataBase
***Will be added Soon***

## 🎗️ Team Member's
- Dharaneesh J
- Atchaya  V  S
- Varshini  N  P
- Gunavarthan   S

## 📜 License

This project is licensed under the MIT License. See the [LICENSE](LICENSE.md) file for more details.
