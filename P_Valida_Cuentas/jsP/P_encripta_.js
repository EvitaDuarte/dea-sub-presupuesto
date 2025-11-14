async function encriptaNativo(textoPlano) {
    const encoder = new TextEncoder();
    const data = encoder.encode(textoPlano);
    
    // 1. Generar la Clave (AES-256)
    // Usaremos una clave de 256 bits (32 bytes)
    const key = await window.crypto.subtle.generateKey(
        { name: "AES-CBC", length: 256 },
        true, // Exportable
        ["encrypt", "decrypt"]
    );

    // 2. Generar el Vector de Inicialización (IV) de 16 bytes (128 bits)
    const iv = window.crypto.getRandomValues(new Uint8Array(16));

    // 3. Encriptar los datos
    const encryptedData = await window.crypto.subtle.encrypt(
        { name: "AES-CBC", iv: iv },
        key,
        data
    );

    // 4. Exportar la clave para enviarla
    const rawKey = await window.crypto.subtle.exportKey(
        "raw",
        key
    );

    // 5. Codificar los componentes a Base64 y unirlos (IV|Key|Cifrado)
    const ivBase64          = btoa(String.fromCharCode(...new Uint8Array(iv)));
    const keyBase64         = btoa(String.fromCharCode(...new Uint8Array(rawKey)));
    const encryptedBase64   = btoa(String.fromCharCode(...new Uint8Array(encryptedData)));

    return ivBase64 + '|' + keyBase64 + '|' + encryptedBase64;
}