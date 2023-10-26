const { Client, GatewayIntentBits, Partials } = require("discord.js");
const INTENTS = Object.values(GatewayIntentBits);
const PARTIALS = Object.values(Partials);
const client = new Client({
    intents: INTENTS,
    allowedMentions: {
        parse: ["users"]
    },
    partials: PARTIALS,
    retryLimit: 3
});
const mysql = require('mysql');
const token = '';
const db = mysql.createConnection({
    host: '127.0.0.1',
    user: 'TR8697',
    password: '',
    database: 'hesapesle'
});

const usedCodes = new Set();

db.connect(err => {
    if (err) {
        console.error('MySQL bağlantı hatası:', err);
        return;
    }
    console.log('MySQL bağlantısı başarılı.');
});

client.on('ready', () => {
    console.log(`[BILGI] Bot aktif.`);
    client.user.setActivity("Test")
});

client.on('messageCreate', async (message) => {
    if (message.author.bot) return;

    const kanalID = '988372953521606686';
    const kod = message.content; 

    console.log(`Alınan mesaj: "${kod}"`);

    if (message.channel.id == kanalID) {

        if (usedCodes.has(kod)) {
            message.reply('Bu kod daha önce kullanıldı.');
            return;
        }

        const veriler = await getKullaniciVerileriFromMySQL(kod);

        if (veriler !== null) {
            console.log('Database "heseyi":', veriler.heseyi);

 
            if (veriler.heseyi === "Onaylandı") {
                message.reply('Bu kod daha önce onaylandı.');
                return;
            }

            const onayliRolID = '1008091990786850897';
            const kullanici = message.member;

            try {
                const guild = message.guild;
                const onayliRol = guild.roles.cache.get(onayliRolID);

                if (onayliRol !== undefined) {
                    await kullanici.roles.add(onayliRol);
                    console.log('Rol başarıyla eklendi.');


                    await updateHeseyiInDatabase(kod, "Onaylandı");

                    message.reply('Başarılı bir şekilde onaylandınız ve onaylı rolü aldınız.');

                    usedCodes.add(kod);
                } else {
                    message.reply('Onaylı rol bulunamadı. Lütfen ayarları kontrol edin.');
                }
            } catch (error) {
                console.error('Rol ekleme hatası:', error);
                message.reply('Bir hata oluştu, lütfen daha sonra tekrar deneyin.');
            }
        } else {
            message.reply('Geçersiz onay kodu.');
        }
    }
});

async function getKullaniciVerileriFromMySQL(kod) {
    return new Promise((resolve, reject) => {
        const query = 'SELECT player_name, heseyi FROM heseyi_data WHERE heseyi = ?';

        db.query(query, [kod], (err, results) => {
            if (err) {
                console.error('Kullanıcı verileri alma hatası:', err);
                reject(err);
            } else {
                const veriler = results[0] ? results[0] : null;
                resolve(veriler);
            }
        });
    });
}

async function updateHeseyiInDatabase(kod, newValue) {
    return new Promise((resolve, reject) => {
        const updateQuery = 'UPDATE heseyi_data SET heseyi = ? WHERE heseyi = ?';

        db.query(updateQuery, [newValue, kod], (err, results) => {
            if (err) {
                console.error('Heseyi güncelleme hatası:', err);
                reject(err);
            } else {
                console.log('Heseyi veritabanında güncellendi:', newValue);
                resolve();
            }
        });
    });
}

client.login(token);
