class CsvExporter {
  static exportCsv(data, delimiter = ';') {

    let csvContent = "data:text/csv;charset=utf-8,"
        + data.map(e => e.join(delimiter)).join("\n");

    let encodedUri = encodeURI(csvContent);
    window.open(encodedUri);

    const link = document.createElement('a');
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "twitch-light.csv");
    document.body.appendChild(link); // Required for FF

    link.click();

    document.body.removeChild(link);
  }
}

export default CsvExporter;