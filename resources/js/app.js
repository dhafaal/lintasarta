import './bootstrap';
import './calendar.js';
import { createIcons, Home, Users, Calendar, Clock, CheckCircle, CalendarDays, LogOut, User } from 'lucide';

createIcons({
    icons: {
        Home,
        Users,
        Calendar,
        Clock,
        CheckCircle,
        CalendarDays,
        LogOut,
        User
    }
});

// Hapus import CSS FullCalendar lama
// import '@fullcalendar/core/styles/index.css';
// import '@fullcalendar/daygrid/styles/index.css';
