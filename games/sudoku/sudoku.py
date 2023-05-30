import mysql.connector
import math
import random
import time
time = time.time()

NUMBEROFSHUFFLES = 20000;

debug = 0
useDB = 0

if useDB == 1:
    db = mysql.connector (
        host = "localhost",
        user = "localSQL",
        password = "xxxxxx",
        database = "sudoku"
    )
    cursor = db.cursor()

# fill the grid
def fillGrid(a,b,grid):
    v = [1, 2, 3, 4, 5, 6, 7, 8, 9]
    random.shuffle(v)

    for r in v:
        grid[a][b] = r
        if checkCRB(a,b,grid):
            if debug == 1:
                print(a, " ", b, " ", r)
            if b == 8:
                d = 0 
            else:
                d = b + 1
            if d == 0:
                c = a + 1
            else:
                c = a
            if c == 9:
                return grid
        else:
            grid[a][b] = 0
            continue
        g = fillGrid(c,d,grid)
        if not g:
            continue
        else:
            return g
    return False

#check for duplicates in rows
def checkR(a, b, grid):
    for i in range(9):
        if i != b and grid[a][i] == grid[a][b]:
            return False
    return True

#check for duplicates in columns
def checkC(a, b, grid):
    for i in range(9):
        if i != a and grid[i][b] == grid[a][b]:
            return False
    return True

# check for duplicates in blocks
def checkB(a, b, grid):
    c = math.floor(a / 3) * 3
    d = math.floor(b / 3) * 3
    c2 = c + 3
    d2 = d + 3
    for e in range(c,c2):
        for f in range(d,d2):
            if a == e and b == f:
                continue
            else:
                if grid[a][b] == grid[e][f]:
                    return False
    return True

# initiate checking for duplicates in row column and block
def checkCRB(a, b, grid):
    if not checkC(a,b,grid):
        return False
    if not checkR(a,b,grid):
        return False
    if not checkB(a,b,grid):
        return False
    return True

# initiate shuffle
def shuffleGrid(grid):
    for i in range(NUMBEROFSHUFFLES):
        t = random.randrange(0,4)
        if t == 0:
            shuffle1(grid)
        if t == 1:
            shuffle2(grid)
        if t == 2:
            shuffle3(grid)
        if t == 3:
            shuffle4(grid)
    return grid

# mutual exchange of two digits
def shuffle1(grid):
    t1 = [random.randrange(1,10)]
    t2 = [random.randrange(1,10)]
    if t1[0] == t2[0]:
        if t1[0] < 5:
            t2[0] = t1[0] + 1
        else:
            t2[0] = t1[0] - 1

    for i in range(9):
        for j in range(9):
            if grid[i][j] == t1[0]:
                grid[i][j] = t2[0]
            elif grid[i][j] == t2[0]:
                grid[i][j] = t1[0]
    return grid

# mutual exchange of two columns in the same column of blocks
def shuffle2(grid):
    trashGrid = [[0 for columns in range(9)] for rows in range(9)]

    for i in range(9):
        for j in range(9):
            trashGrid[i][j] = grid[i][j]

    for k in range(0,9,3):
        t1 = random.randrange(0,3)
        x = random.randrange(1,3)
        if t1 == 0:
            t2 = t1 + x
        elif t1 == 1:
            if x == 1:
                t2 = t1 - 1
            else:
                t2 = t1 + 1
        elif t1 == 2:
            t2 = t1 - x
        for l in range(9):
            grid[l][t2 + k] = trashGrid[l][t1 + k]
        for m in range(9):
            grid[m][t1 + k] = trashGrid[m][t2 + k]
    return grid

# mutual exchange of two columns of blocks
def shuffle3(grid):
    trashGrid = [[0 for columns in range(9)] for rows in range(9)]

    t1 = random.randrange(0,3)
    x = random.randrange(1,3)
    if t1 == 0:
        t2 = t1 + x
    elif t1 == 1:
        if x == 1:
            t2 = t1 - 1
        else:
            t2 = t1 + 1
    elif t1 == 2:
        t2 = t1 - x
    for i in range(9):
        for j in range(9):
            trashGrid[i][j] = grid[i][j]
    for k in range(9):
        grid[k][t2 * 3] = trashGrid[k][t1 * 3]
        grid[k][t2 * 3 + 1] = trashGrid[k][t1 * 3 + 1]
        grid[k][t2 * 3 + 2] = trashGrid[k][t1 * 3 + 2]
    for l in range(9):
        grid[l][t1 * 3] = trashGrid[l][t2 * 3];
        grid[l][t1 * 3 + 1] = trashGrid[l][t2 * 3 + 1];
        grid[l][t1 * 3 + 2] = trashGrid[l][t2 * 3 + 2];
    return grid

# grid rolling
def shuffle4(grid):
    trashGrid = [[0 for columns in range(9)] for rows in range(9)]

    for i in range(9):
        for j in range(9):
            trashGrid[i][j] = grid[i][j]
    for k in range(9):
        for l in range(9):
            a = 0 + l
            b = 8 - k
            grid[a][b] = trashGrid[k][l]
    return grid

# erase cells to create the game board
def eraseCells(g):
    v = [i for i in range(81)]
    random.shuffle(v)
    for j in range(81):
        a = math.floor(v[j] / 9)
        b = v[j] % 9
        if debug == 1:
            print("a =",a,"b =",b,"block =",v[j],"v =", grid[a][b])
        hold = g[a][b]
        g[a][b] = 0
        p = playable(a,b,hold,g)
        if p:
            continue
        else:
            g[a][b] = hold
        if debug == 1:
            print("playable=",p)
            print(g)
    return g

# check to see if the puzzle is playable
def playable(a,b,x,grid):
    g = [[0 for columns in range(9)] for rows in range(9)]
    for k in range(9):
        for l in range(9):
            g[k][l] = grid[k][l]
    for i in range(1,10):
        if i == x:
            continue
        else:
            g[a][b] = i
            if checkCRB(a,b,g):
                if solved(0,0,g):
                    return False
                else:
                    g[a][b] = 0
            else:
                g[a][b] = 0
    return True

# check to see if the puzzle is solvable
def solved(a,b,g):
    if b == 8:
        d = 0
    else:
        d = b + 1
    if d == 0:
        c = a + 1
    else:
        c = a
    if c == 9:
        return True

    if g[a][b] == 0:
        for i in range(1,10):
            g[a][b] = i
            if checkCRB(a,b,g):
                if solved(c,d,g):
                    return True
    else:
        if solved(c,d,g):
            return True
        
    return False

# start checking the diffulculty level
def findLevel(grid):
    c = restrictC(grid)
    r = restrictR(grid)
    n = restrictNum(grid)
    a = (c + r) / 2
    l = 0
    m = 0
    if n > 35:
        l = 1
    elif n <=35 and n > 30:
        l = 2
    elif n <=30 and n > 25:
        l = 3
    elif n <=25 and n > 20:
        l = 4
    elif n <= 20:
        l = 5
    if a > 4:
        m = 1
    elif a <= 4 and a > 3:
        m = 2
    elif a <= 3 and a > 2:
        m = 3
    elif a <= 2 and a > 1:
        m = 4
    elif a <= 1:
        m = 5
    x = math.ceil((l + m) / 2)
    return x

# check the column restriction
def restrictR(grid):
    x = 9
    for i in range(9):
        y = 0
        for j in range(9):
            if grid[i][j] != 0:
                y += 1
        if y < x:
            x = y
    return x

#check the row restriction
def restrictC(grid):
    x = 9
    for i in range(9):
        y = 0
        for j in range(9):
            if grid[j][i] != 0:
                y += 1
        if y < x:
            x = y
    return x

# check the total count restriction
def restrictNum(grid):
    x = 0
    for i in range(9):
        for j in range(9):
            if grid[i][j] != 0:
                x += 1
    return x

# init
number = int(input("Number of grids to make? "))

gridZ = [[0 for columns in range(9)] for rows in range(9)]
key = [[0 for columns in range(9)] for rows in range(9)]
game = [[0 for columns in range(9)] for rows in range(9)]

for h in range(number):
    grid = fillGrid(0,0,gridZ)
    if debug == 1:
        print("grid")
        print(grid)
    key = shuffleGrid(grid)
    if debug == 1:
        print("key")
        print(key)
    dbKey = ""
    for i in range(9):
        for j in range(9):
            dbKey = dbKey + str(key[i][j])
    game = eraseCells(key)
    if debug == 1:
        print("game")
        print(game)
    dbGame = ""
    for k in range(9):
        for l in range(9):
            dbGame = dbGame + str(game[k][l])
    if debug == 1:
        print(dbKey)
        print(dbGame)
    level = findLevel(game)
    fileT = str(time).split('.')
    if useDB == 1:
        sql = "INSERT INTO sudoku (id,difficulty,keyBoard,gameBoard,playCount,dateCreated,notUsed1,notUsed2) VALUES (%s,%s,%s,%s,%s,%s,%s)"
        val = (0,level,dbKey,dbGame,0,fileT[0],0,0)
        cursor.execute(sql,val)
        db.commit()
    if useDB == 0:
        fileL = str(level)
        file1 = open("boards.csv","a")
        file1.write("NULL," + fileL + "," + dbKey + "," + dbGame + ",0," + fileT[0] + ",0,0\n")
        file1.close()

print("Successfully created", number, "puzzles")

