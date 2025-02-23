import Home from "../components/icons/menu/home"
import BankQuestions from "../components/icons/menu/bankQuestions"
import Attachment from "../components/icons/menu/attachment"

import BarChartRoundedIcon from '@mui/icons-material/BarChartRounded';
import PersonRoundedIcon from '@mui/icons-material/PersonRounded';
import SettingsOutlinedIcon from '@mui/icons-material/SettingsOutlined';

export const menu = [
    {
        name: "Home",
        path: "",
        icon: <Home />
    },
    {
        name: "Hasil Ujian",
        path: "hasil-ujian",
        icon: <BankQuestions />
    },
    {
        name: "Grafik Hasil Ujian",
        path: "presentase",
        icon: <BarChartRoundedIcon />
    },
    {
        name: "Riwayat Ujian",
        path: "riwayat-ujian",
        icon: <Attachment />
    },
    {
        name: "My Profile",
        path: "profile",
        icon: <PersonRoundedIcon />
    },
    {
        name: "Pengaturan",
        path: "pengaturan",
        icon: <SettingsOutlinedIcon />
    },
]