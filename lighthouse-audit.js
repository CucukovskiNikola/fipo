import { exec } from "child_process";
import path from "path";

// Specify the URL of the Laravel app, assuming it runs locally or on a staging server
const url = "http://localhost:8000"; // Adjust this URL to your local or deployed app

// Path to the Lighthouse binary or command
const lighthouseCommand = `lighthouse ${url} --output html --output-path ./lighthouse-report.html`;

exec(lighthouseCommand, (error, stdout, stderr) => {
  if (error) {
    console.error(`exec error: ${error}`);
    return;
  }
  if (stderr) {
    console.error(`stderr: ${stderr}`);
    return;
  }
  console.log(`stdout: ${stdout}`);
  console.log("Lighthouse report generated as lighthouse-report.html");
});
