const validateEmail=(email)=>{
    return  /[\w-\.]{2,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/.test(email.trim());
}

const validatePassword=(password)=>{
    return /^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])([a-zA-Z0-9]{8,16})$/.test(password.trim());
}

const validateUserNAme=(userName)=>{
    return /^[a-z ñáéíóú-]{2,60}$/i.test(userName.trim());
}