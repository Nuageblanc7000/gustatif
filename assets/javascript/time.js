/**
 * CREATE TIMER PAGE SCHEDULE
 * VERSION 1.0 R.W
 */
const  container_timer = document.querySelector('.js-timer')
const  minute = container_timer.querySelector('.js-m')
const  hour = container_timer.querySelector('.js-h')
const  second = container_timer.querySelector('.js-s')




const time = new Date();

let hours = time.getHours();
let min = time.getMinutes();
let sec = time.getSeconds();

function timer(){
    sec = sec + 1
    if(sec === 60){
        min = min +1
        sec = 0
    }
    if(sec < 10){
        second.innerHTML = '0' +sec
    }else{
        second.innerHTML = sec

    }

    if(min == 60){
        hours = hours +1
        min = 0
    }
    if(min < 10){
        minute.innerHTML = '0' +min
    }else{
        minute.innerHTML = min
    }
    if(hours == 24){
        hours = 0
    }
    if(hours < 10){
        hour.innerHTML = '0' + hours
    }else{
        
        hour.innerHTML =  hours
    }
    
}
const interTime = setInterval(timer,1000);
timer();
