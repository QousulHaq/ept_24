import UserProfessional from "../components/icons/UserProfessional"
import UserMahasiswa from "../components/icons/UserMahasiswa"
import UserPelajar from "../components/icons/UserPelajar"

export const card1 = [
    {
        nama: "Professional",
        description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit",
        icon: (props) => <UserProfessional {...props} />,
        primaryColor: "primary1",
        secondaryColor: "secondary6",
    },
    {
        nama: "Mahasiswa",
        description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit",
        icon: (props) => <UserMahasiswa {...props} />,
        primaryColor: "primary2",
        secondaryColor: "secondary5",
    },
    {
        nama: "Pelajar",
        description: "Lorem ipsum dolor sit amet, consectetur adipiscing elit",
        icon: (props) => <UserPelajar {...props} />,
        primaryColor: "primary3",
        secondaryColor: "secondary7",
    }
]

export const tipeUjian = {
    Professional: [
        {
            title: "CPNS",
            noUjian: "2024/08-3493-24",
            jadwal: "Jumat, 5 Juli 2024",
            waktu: "09:00 - 11:00 WIB",
            primaryColor: "primary1",
            secondaryColor: "secondary6",
        },
        {
            title: "CPNS",
            noUjian: "2024/08-3493-24",
            jadwal: "Jumat, 5 Juli 2024",
            waktu: "09:00 - 11:00 WIB",
            primaryColor: "primary1",
            secondaryColor: "secondary6",
        },
        {
            title: "CPNS",
            noUjian: "2024/08-3493-24",
            jadwal: "Jumat, 5 Juli 2024",
            waktu: "09:00 - 11:00 WIB",
            primaryColor: "primary1",
            secondaryColor: "secondary6",
        }
    ],
    Mahasiswa: [
        {
            title: "TOEIC",
            noUjian: "2024/08-3493-24",
            jadwal: "Jumat, 5 Juli 2024",
            waktu: "09:00 - 11:00 WIB",
            primaryColor: "primary2",
            secondaryColor: "secondary5",
        },
        {
            title: "TOEIC",
            noUjian: "2024/08-3493-24",
            jadwal: "Jumat, 5 Juli 2024",
            waktu: "09:00 - 11:00 WIB",
            primaryColor: "primary2",
            secondaryColor: "secondary5",
        },
        {
            title: "TOEIC",
            noUjian: "2024/08-3493-24",
            jadwal: "Jumat, 5 Juli 2024",
            waktu: "09:00 - 11:00 WIB",
            primaryColor: "primary2",
            secondaryColor: "secondary5",
        }
    ],
    Pelajar: [
        {
            title: "UTBK",
            noUjian: "2024/08-3493-24",
            jadwal: "Jumat, 5 Juli 2024",
            waktu: "09:00 - 11:00 WIB",
            primaryColor: "primary3",
            secondaryColor: "secondary7",
        },
        {
            title: "UTBK",
            noUjian: "2024/08-3493-24",
            jadwal: "Jumat, 5 Juli 2024",
            waktu: "09:00 - 11:00 WIB",
            primaryColor: "primary3",
            secondaryColor: "secondary7",
        },
        {
            title: "UTBK",
            noUjian: "2024/08-3493-24",
            jadwal: "Jumat, 5 Juli 2024",
            waktu: "09:00 - 11:00 WIB",
            primaryColor: "primary3",
            secondaryColor: "secondary7",
        }
    ],
}