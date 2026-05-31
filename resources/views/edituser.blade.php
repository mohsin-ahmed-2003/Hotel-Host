<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD APPLIACTION</title>
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
</head>

<body>
    <div id="update-modal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h3>Update Employee</h3>

            <form id="update" enctype="multipart/form-data">
                <label for="up_emp_id">Employee Id: </label>
                <input type="number" id="up-emp_id" name="up_emp_id" required readonly><br>

                <label for="up-name">Enter Name:</label>
                <input type="text" id="up-name" name="up_name" required><br>
                <p id="up-name-error" class="error"></p>

                <label for="up-dob">Enter Date of Birth:</label>
                <input type="date" id="up-dob" name="up_dob" required><br>
                <p id="up-dob-error" class="error"></p>

                <input type="radio" id="male" name="gender" value="Male">
                <label for="html">Male</label><br>
                <input type="radio" id="female" name="gender" value="female">
                <label for="female">Female</label><br>
                <input type="radio" id="other" name="gender" value="other">
                <label for="other">Other</label>
                <p id="up-dob-error" class="error"></p>

                <label for="up-dept">Enter Department:</label>
                <input type="text" id="up-dept" name="up_dept"><br>
                <p id="up-dept-error" class="error"></p>

                <label>Current Pictures:</label>
                <div id="existing-pics-container"></div>

                <label for="up-pic">Select New Picture(s):</label>
                <input type="file" id="up-pic" name="up_pic[]" multiple accept=".jpg, .jpeg, .png"><br>
                <p id="up-pic-error" class="error"></p>

                <label>New Pictures Preview:</label>
                <div id="new-pics-preview"></div>

                <button type="submit" id="update-btn">Update</button>
                <p id="err1"></p>
            </form>
        </div>
    </div>
</body>

</html>