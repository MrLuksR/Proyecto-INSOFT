/* INADI | CONVENIOS
   Complementa Inicio/CSS/stylesinicio.css y sigue el estilo de Nosotros.
*/

.about-header {
    margin-bottom: 28px;
}

.about-label {
    display: inline-block;
    margin-bottom: 8px;
    color: var(--primary, #45459c);
    font-size: 11px;
    font-weight: 800;
    letter-spacing: 1.4px;
}

.about-header h1 {
    margin-bottom: 8px;
    color: var(--text-dark, #252542);
    font-size: clamp(27px, 3vw, 36px);
    line-height: 1.25;
}

.about-header h1 strong {
    color: var(--primary, #45459c);
}

.about-header p {
    color: var(--text-muted, #77778d);
    font-size: 14px;
    line-height: 1.7;
}

/* Presentación */
.about-intro {
    display: grid;
    grid-template-columns: 110px minmax(0, 1fr);
    align-items: start;
    gap: 26px;
    margin-bottom: 30px;
    padding: 30px;
    background: #fff;
    border: 1px solid rgba(69, 69, 156, .06);
    border-radius: var(--radius-lg, 18px);
    box-shadow: var(--shadow-sm, 0 5px 20px rgba(30, 30, 80, .06));
}

.agreement-intro-icon {
    width: 100px;
    height: 100px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 22px;
    color: var(--primary, #45459c);
    background: var(--primary-soft, #eeeeff);
    font-size: 42px;
}

.about-text h2 {
    margin-bottom: 15px;
    color: var(--text-dark, #252542);
    font-size: 23px;
    line-height: 1.35;
}

.about-text p {
    margin-bottom: 14px;
    color: #626277;
    font-size: 14px;
    line-height: 1.85;
}

.about-text p:last-child {
    margin-bottom: 0;
}

/* Secciones */
.about-section {
    margin-bottom: 26px;
    padding: 28px;
    background: #fff;
    border: 1px solid rgba(69, 69, 156, .06);
    border-radius: var(--radius-lg, 18px);
    box-shadow: var(--shadow-sm, 0 5px 20px rgba(30, 30, 80, .06));
}

.section-heading {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 22px;
}

.section-icon {
    width: 48px;
    height: 48px;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 13px;
    background: var(--primary-soft, #eeeeff);
    color: var(--primary, #45459c);
    font-size: 19px;
}

.section-heading span {
    color: var(--text-muted, #77778d);
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 1px;
}

.section-heading h2 {
    margin-top: 3px;
    color: var(--text-dark, #252542);
    font-size: 21px;
    line-height: 1.4;
}

/* Tarjetas de convenios */
.agreements {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 15px;
}

.agreement-card {
    min-width: 0;
    display: flex;
    align-items: flex-start;
    gap: 13px;
    padding: 19px;
    border: 1px solid rgba(69, 69, 156, .08);
    border-radius: var(--radius-md, 14px);
    color: #626277;
    background: #f8f8ff;
    font-size: 13px;
    line-height: 1.7;
    transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
}

.agreement-card:hover {
    transform: translateY(-3px);
    background: #fff;
    box-shadow: var(--shadow-md, 0 10px 25px rgba(30, 30, 80, .10));
}

.agreement-icon {
    width: 39px;
    height: 39px;
    flex: 0 0 39px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 11px;
    color: var(--primary, #45459c);
    background: var(--primary-soft, #eeeeff);
    font-size: 16px;
}

.agreement-card h3 {
    margin: 1px 0 7px;
    color: var(--text-dark, #252542);
    font-size: 14px;
    line-height: 1.5;
}

.agreement-card p {
    margin: 0;
    color: #626277;
    font-size: 12px;
    line-height: 1.75;
}

/* Instituciones relacionadas */
.institution-list {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
}

.institution-item {
    min-width: 0;
    display: flex;
    align-items: flex-start;
    gap: 11px;
    padding: 15px;
    border: 1px solid rgba(69, 69, 156, .07);
    border-radius: var(--radius-sm, 10px);
    color: #626277;
    background: #f8f8ff;
    font-size: 13px;
    line-height: 1.7;
    transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
}

.institution-item:hover {
    transform: translateY(-2px);
    background: var(--primary-soft, #eeeeff);
    box-shadow: var(--shadow-sm, 0 5px 20px rgba(30, 30, 80, .06));
}

.institution-item > i {
    flex: 0 0 20px;
    margin-top: 4px;
    color: var(--primary, #45459c);
    font-size: 13px;
}

.agreements-note {
    margin-top: 16px;
    color: var(--text-muted, #77778d);
    font-size: 12px;
    line-height: 1.7;
}

/* Cierre institucional */
.about-final {
    display: flex;
    align-items: flex-start;
    gap: 20px;
    margin-top: 28px;
    padding: 30px;
    border-radius: var(--radius-lg, 18px);
    color: #fff;
    background: linear-gradient(120deg, var(--primary-deep, #30306f), var(--primary, #45459c));
    box-shadow: var(--shadow-md, 0 10px 25px rgba(30, 30, 80, .10));
}

.final-icon {
    width: 51px;
    height: 51px;
    flex: 0 0 51px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(255, 255, 255, .15);
    border-radius: 14px;
    color: var(--orange, #f3a33b);
    background: rgba(255, 255, 255, .12);
    font-size: 22px;
}

.final-label {
    display: block;
    margin-bottom: 5px;
    color: #ffce79;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 1.2px;
}

.about-final h2 {
    margin-bottom: 10px;
    color: #fff;
    font-size: 23px;
}

.about-final p {
    color: rgba(255, 255, 255, .85);
    font-size: 13px;
    line-height: 1.85;
}

.courses-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    margin-top: 14px;
    padding: 12px 17px;
    border: 1px solid var(--primary, #45459c);
    border-radius: 10px;
    color: #fff;
    background: var(--primary, #45459c);
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    transition: background .2s ease, transform .2s ease;
}

.courses-link:hover {
    color: #fff;
    background: var(--primary-dark, #33337c);
    transform: translateY(-2px);
}

.agreement-contact {
    color: var(--primary, #45459c);
    background: #fff;
    border-color: #fff;
}

.agreement-contact:hover {
    color: var(--primary, #45459c);
    background: #f2f2ff;
}

/* Responsive */
@media (max-width: 1250px) {
    .about-intro {
        gap: 22px;
        padding: 24px;
    }

    .agreements {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 1050px) {
    .about-intro {
        grid-template-columns: 1fr;
    }

    .agreement-intro-icon {
        width: 82px;
        height: 82px;
        font-size: 34px;
    }

    .institution-list {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 760px) {
    .about-header {
        margin-bottom: 22px;
    }

    .about-header h1 {
        font-size: 25px;
    }

    .about-header p {
        font-size: 13px;
    }

    .about-intro,
    .about-section {
        padding: 19px;
        border-radius: 15px;
    }

    .about-intro {
        gap: 20px;
        margin-bottom: 22px;
    }

    .about-text h2 {
        font-size: 20px;
    }

    .about-text p {
        font-size: 13px;
    }

    .section-heading {
        align-items: flex-start;
        gap: 11px;
    }

    .section-icon {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }

    .section-heading h2 {
        font-size: 17px;
    }

    .institution-item,
    .agreement-card {
        padding: 12px;
        font-size: 12px;
    }

    .agreements {
        grid-template-columns: 1fr;
    }

    .about-final {
        gap: 13px;
        padding: 19px;
    }

    .final-icon {
        width: 40px;
        height: 40px;
        flex-basis: 40px;
        font-size: 18px;
    }

    .about-final h2 {
        font-size: 18px;
    }

    .about-final p {
        font-size: 12px;
    }
}

@media (max-width: 400px) {
    .about-intro,
    .about-section {
        padding: 14px;
    }

    .section-heading h2 {
        font-size: 16px;
    }

    .about-final {
        flex-direction: column;
    }
}
