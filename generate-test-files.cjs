const fs = require('fs');
const { v4: uuid } = require('uuid');

if (!fs.existsSync('test/')) fs.mkdirSync('test/');

for (i = 0; i < 10000; i++) {
    fs.writeFileSync(`${uuid()}.txt`, `${new Date().toString()}`);
    console.log(`./test/${i}/10000`);
}

console.log('done');
