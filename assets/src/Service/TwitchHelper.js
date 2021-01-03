class TwitchHelper {
  static makeTwitchUrl(videoId, offset) {
    return `https://www.twitch.tv/videos/${videoId}?t=${offset}s`;
  }

  static convertSerMessage(msg, emoticons) {
    let result = msg;

    if (emoticons !== null) {
      let finds = [];
      emoticons.forEach((currentValue) => {
        finds.push(result.slice(currentValue.begin, currentValue.end + 1));
      });

      finds.forEach((currentValue, index) => {
        result = result.replace(currentValue,
            `<img src='https://static-cdn.jtvnw.net/emoticons/v2/${emoticons[index]._id}/default/light/1.0' alt="${currentValue}">`);
      });
    }

    return result;
  }
}

export default TwitchHelper;