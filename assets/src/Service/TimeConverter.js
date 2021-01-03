class TimeConverter {
  static convert(offset) {
    let date = new Date(offset * 1000);

    let minutes = date.getMinutes().toString();
    if (minutes.length === 1) {
      minutes = '0' + minutes;
    }

    let seconds = date.getSeconds().toString();
    if (seconds.length === 1) {
      seconds = '0' + seconds;
    }

    let hours = (date.getHours() - 3).toString();
    if (hours.length === 1) {
      hours = '0' + hours;
    }

    return hours + ':' + minutes + ':' + seconds;
  }
}

export default TimeConverter;