# Shift Logic Testing Guide

## 🧪 **Test Scenarios**

### **Test Case 1: Shift 1 = Pagi**

**Input:**
```json
POST /admin/schedules/get-available-shifts
{
    "first_shift_id": 1  // Assuming ID 1 is a "Pagi" shift
}
```

**Expected Output:**
```json
{
    "shifts": [
        {
            "id": 2,
            "shift_name": "Shift Siang A",
            "category": "Siang",
            "start_time": "13:00:00",
            "end_time": "21:00:00"
        },
        {
            "id": 3,
            "shift_name": "Shift Siang B", 
            "category": "Siang",
            "start_time": "14:00:00",
            "end_time": "22:00:00"
        }
    ]
}
```

**Frontend Behavior:**
- Dropdown 2 shows only "Siang" category shifts
- Dropdown 2 is enabled
- User can select any "Siang" shift

---

### **Test Case 2: Shift 1 = Siang**

**Input:**
```json
POST /admin/schedules/get-available-shifts
{
    "first_shift_id": 2  // Assuming ID 2 is a "Siang" shift
}
```

**Expected Output:**
```json
{
    "shifts": [
        {
            "id": 4,
            "shift_name": "Shift Malam A",
            "category": "Malam", 
            "start_time": "22:00:00",
            "end_time": "06:00:00"
        },
        {
            "id": 5,
            "shift_name": "Shift Malam B",
            "category": "Malam",
            "start_time": "23:00:00", 
            "end_time": "07:00:00"
        }
    ]
}
```

**Frontend Behavior:**
- Dropdown 2 shows only "Malam" category shifts
- Dropdown 2 is enabled
- User can select any "Malam" shift

---

### **Test Case 3: Shift 1 = Malam**

**Input:**
```json
POST /admin/schedules/get-available-shifts
{
    "first_shift_id": 4  // Assuming ID 4 is a "Malam" shift
}
```

**Expected Output:**
```json
{
    "shifts": []
}
```

**Frontend Behavior:**
- Dropdown 2 shows "-- Tidak tersedia --"
- Dropdown 2 is disabled
- User cannot select any second shift

---

## 🔍 **Manual Testing Steps**

### **Step 1: Test Pagi → Siang**
1. Go to `/admin/schedules/create`
2. Select any day in calendar
3. In first dropdown, select a shift with category "Pagi"
4. Observe second dropdown updates to show only "Siang" shifts
5. Verify you can select a "Siang" shift in second dropdown

### **Step 2: Test Siang → Malam**
1. In same calendar day
2. Change first dropdown to a shift with category "Siang"  
3. Observe second dropdown updates to show only "Malam" shifts
4. Verify you can select a "Malam" shift in second dropdown

### **Step 3: Test Malam → None**
1. In same calendar day
2. Change first dropdown to a shift with category "Malam"
3. Observe second dropdown shows "-- Tidak tersedia --"
4. Verify second dropdown is disabled (cannot click)

### **Step 4: Test Edit Page**
1. Go to `/admin/schedules/{id}/edit` for monthly schedule
2. Repeat steps 1-3 above
3. Verify same behavior in edit mode

---

## 🐛 **Debugging Tips**

### **If Logic Not Working:**

1. **Check Shift Categories in Database:**
```sql
SELECT id, shift_name, category FROM shifts;
```

2. **Check API Response:**
```javascript
// Open browser console and check network tab
// Look for POST request to get-available-shifts
// Verify response matches expected format
```

3. **Check JavaScript Console:**
```javascript
// Look for any JavaScript errors
// Check if updateSecondDropdown function is called
// Verify CSRF token is present
```

4. **Test API Directly:**
```bash
curl -X POST http://localhost/admin/schedules/get-available-shifts \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: your-token" \
  -d '{"first_shift_id": 1}'
```

---

## ✅ **Expected Results Summary**

| First Shift Category | Second Shift Options | Dropdown State |
|---------------------|---------------------|----------------|
| **Pagi** | Only "Siang" shifts | Enabled |
| **Siang** | Only "Malam" shifts | Enabled |
| **Malam** | No options | Disabled |
| **Empty/None** | "-- Shift 2 --" | Enabled but empty |

---

## 🎯 **Success Criteria**

- ✅ Pagi shifts only allow Siang as second shift
- ✅ Siang shifts only allow Malam as second shift  
- ✅ Malam shifts disable second dropdown completely
- ✅ API returns correct filtered shifts
- ✅ Frontend updates dropdowns dynamically
- ✅ Error handling works if API fails
- ✅ Same behavior in both create and edit views

**All tests should pass for the shift logic to be considered working correctly.**
